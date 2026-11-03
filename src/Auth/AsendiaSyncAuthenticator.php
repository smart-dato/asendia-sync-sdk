<?php

namespace SmartDato\AsendiaSync\Auth;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Carbon;
use Saloon\Contracts\Authenticator;
use Saloon\Http\PendingRequest;
use SmartDato\AsendiaSync\Exceptions\AsendiaSyncApiException;
use SmartDato\AsendiaSync\Requests\Auth\AuthenticateRequest;

/**
 * Logs in with POST /api/authenticate and sends the returned JWT as bearer
 * token. Asendia issues the token for 24 hours; it is kept on the instance and
 * in the cache, keyed per login, until shortly before its `exp` claim.
 */
class AsendiaSyncAuthenticator implements Authenticator
{
    private const int EXPIRY_SAFETY_MARGIN_SECONDS = 300;

    private const int FALLBACK_LIFETIME_SECONDS = 3600;

    private ?string $token = null;

    private int $expiresAt = 0;

    public function __construct(
        protected readonly string $username,
        #[\SensitiveParameter]
        protected readonly string $password,
        protected readonly Repository $cache,
    ) {}

    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('Authorization', "Bearer {$this->token($pendingRequest)}");
    }

    public function forgetToken(): void
    {
        $this->token = null;
        $this->expiresAt = 0;

        $this->cache->forget($this->cacheKey());
    }

    private function token(PendingRequest $pendingRequest): string
    {
        $now = Carbon::now()->getTimestamp();

        if (! is_null($this->token) && $now < $this->expiresAt) {
            return $this->token;
        }

        $cached = $this->cache->get($this->cacheKey());

        if (is_array($cached) && is_string($cached['token'] ?? null) && is_int($cached['expiresAt'] ?? null) && $now < $cached['expiresAt']) {
            return $this->remember($cached['token'], $cached['expiresAt']);
        }

        $response = $pendingRequest->getConnector()->send(new AuthenticateRequest($this->username, $this->password));
        $token = $response->json('id_token');

        if (! is_string($token) || $token === '') {
            throw new AsendiaSyncApiException($response, 'The Asendia Sync login answered without an id_token.');
        }

        $expiresAt = $this->expiresAt($token, $now);

        $this->cache->put($this->cacheKey(), ['token' => $token, 'expiresAt' => $expiresAt], max(1, $expiresAt - $now));

        return $this->remember($token, $expiresAt);
    }

    private function remember(string $token, int $expiresAt): string
    {
        $this->token = $token;
        $this->expiresAt = $expiresAt;

        return $token;
    }

    private function expiresAt(string $token, int $now): int
    {
        $payload = explode('.', $token)[1] ?? '';
        $claims = json_decode((string) base64_decode(strtr($payload, '-_', '+/')), true);
        $expiry = is_array($claims) ? ($claims['exp'] ?? null) : null;

        if (! is_int($expiry)) {
            return $now + self::FALLBACK_LIFETIME_SECONDS;
        }

        return max($now, $expiry - self::EXPIRY_SAFETY_MARGIN_SECONDS);
    }

    /**
     * The password is part of the hash so a changed password never reuses the
     * token of the old one.
     */
    private function cacheKey(): string
    {
        $fingerprint = substr(hash('sha256', $this->password), 0, 16);

        return "asendia-sync:token:{$this->username}:{$fingerprint}";
    }
}
