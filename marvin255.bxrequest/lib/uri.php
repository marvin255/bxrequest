<?php

namespace Marvin255\Bxrequest;

use Psr\Http\Message\UriInterface;
use InvalidArgumentException;

/**
 * Объект, который инкапсулирует методы для работы с uri.
 */
class Uri implements UriInterface
{
    /**
     * @var string
     */
    protected $originalUrl = '';
    /**
     * @var array
     */
    protected $parsedUrl = [];
    /**
     * @var array
     */
    protected $standartPorts = [
        'http' => 80,
        'https' => 443,
        'ftp' => 21,
        'ssh' => 22,
    ];

    /**
     * @param string $url
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($url)
    {
        $processedUrl = trim($url);

        $this->originalUrl = $processedUrl;
        $this->parsedUrl = $this->parseUrl($processedUrl);
    }

    /**
     * @inheritdoc
     */
    public function getScheme()
    {
        return $this->parsedUrl['scheme'];
    }

    /**
     * @inheritdoc
     */
    public function getAuthority()
    {
        return $this->parsedUrl['authority'];
    }

    /**
     * @inheritdoc
     */
    public function getUserInfo()
    {
        return $this->parsedUrl['userinfo'];
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->parsedUrl['host'];
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->parsedUrl['port'];
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->parsedUrl['path'];
    }

    /**
     * @inheritdoc
     */
    public function getQuery()
    {
        return $this->parsedUrl['query'];
    }

    /**
     * @inheritdoc
     */
    public function getFragment()
    {
        return $this->parsedUrl['fragment'];
    }

    /**
     * @inheritdoc
     */
    public function withScheme($scheme)
    {
        if ($scheme !== '' && !isset($this->standartPorts[$scheme])) {
            throw new InvalidArgumentException("Wrong scheme: {$scheme}");
        }

        $newParsedUrl = array_merge($this->parsedUrl, ['scheme' => $scheme]);

        return new self($this->implodeUrl($newParsedUrl));
    }

    /**
     * @inheritdoc
     */
    public function withUserInfo($user, $password = null)
    {
        $newParsedUrl = array_merge($this->parsedUrl, [
            'user' => $user,
            'pass' => $password ?: '',
        ]);

        return new self($this->implodeUrl($newParsedUrl));
    }

    /**
     * @inheritdoc
     */
    public function withHost($host)
    {
        $newParsedUrl = array_merge($this->parsedUrl, [
            'host' => $host ?: '',
        ]);

        return new self($this->implodeUrl($newParsedUrl));
    }

    /**
     * @inheritdoc
     */
    public function withPort($port)
    {
        if (!is_null($port) && !is_int($port) && !is_numeric($port)) {
            throw new InvalidArgumentException("Wrong port: {$port}");
        }

        $newParsedUrl = array_merge($this->parsedUrl, [
            'port' => $port ?: '',
        ]);

        return new self($this->implodeUrl($newParsedUrl));
    }

    /**
     * @inheritdoc
     */
    public function withPath($path)
    {
        $newParsedUrl = array_merge($this->parsedUrl, [
            'path' => $path,
        ]);

        return new self($this->implodeUrl($newParsedUrl));
    }

    /**
     * @inheritdoc
     */
    public function withQuery($query)
    {
        $newParsedUrl = array_merge($this->parsedUrl, [
            'query' => $query,
        ]);

        return new self($this->implodeUrl($newParsedUrl));
    }

    /**
     * @inheritdoc
     */
    public function withFragment($fragment)
    {
        $newParsedUrl = array_merge($this->parsedUrl, [
            'fragment' => $fragment,
        ]);

        return new self($this->implodeUrl($newParsedUrl));
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->implodeUrl($this->parsedUrl);
    }

    /**
     * Разбивает ссылку на составные части.
     *
     * @param string $url
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function parseUrl($url)
    {
        $parsedUrl = parse_url($url);
        if ($parsedUrl === false) {
            throw new InvalidArgumentException("Invalid uri: {$url}");
        }

        return [
            'scheme' => !empty($parsedUrl['scheme']) ? strtolower($parsedUrl['scheme']) : '',
            'host' => !empty($parsedUrl['host']) ? strtolower($parsedUrl['host']) : '',
            'path' => !empty($parsedUrl['path']) ? $parsedUrl['path'] : '',
            'query' => !empty($parsedUrl['query']) ? $parsedUrl['query'] : '',
            'fragment' => !empty($parsedUrl['fragment']) ? $parsedUrl['fragment'] : '',
            'user' => !empty($parsedUrl['user']) ? $parsedUrl['user'] : '',
            'pass' => !empty($parsedUrl['pass']) ? $parsedUrl['pass'] : '',
            'port' => $this->parsePort($parsedUrl),
            'userinfo' => $this->parseUserInfo($parsedUrl),
            'authority' => $this->parseAuthority($parsedUrl),
        ];
    }

    /**
     * Возвращает порт из массива, полученного от parse_url.
     *
     * @param array $parsedUrl
     *
     * @return int|null
     */
    protected function parsePort(array $parsedUrl)
    {
        $scheme = !empty($parsedUrl['scheme']) ? strtolower($parsedUrl['scheme']) : '';
        $return = !empty($parsedUrl['port']) ? (int) $parsedUrl['port'] : null;

        if (
            !empty($this->standartPorts[$scheme])
            && $this->standartPorts[$scheme] === $return
        ) {
            $return = null;
        }

        return $return;
    }

    /**
     * Возвращает информацию о пользователе из массива, полученного от parse_url.
     *
     * @param array $parsedUrl
     *
     * @return string
     */
    protected function parseUserInfo(array $parsedUrl)
    {
        $return = '';

        if (!empty($parsedUrl['user'])) {
            $return = $parsedUrl['user'];
            if (!empty($parsedUrl['pass'])) {
                $return .= ":{$parsedUrl['pass']}";
            }
        }

        return $return;
    }

    /**
     * Возвращает фгерщкшен из массива, полученного от parse_url.
     *
     * @param array $parsedUrl
     *
     * @return string
     */
    protected function parseAuthority(array $parsedUrl)
    {
        $return = '';

        if (!empty($parsedUrl['host'])) {
            if ($userinfo = $this->parseUserInfo($parsedUrl)) {
                $return = "{$userinfo}@";
            }
            $return .= $parsedUrl['host'];
            if ($port = $this->parsePort($parsedUrl)) {
                $return .= ":{$port}";
            }
        }

        return $return;
    }

    /**
     * Возвращает ссылку, собранную из массива, полученного от parse_url.
     *
     * @param array $parsedUrl
     *
     * @return string
     */
    protected function implodeUrl(array $parsedUrl)
    {
        $return = '';

        if (!empty($parsedUrl['scheme'])) {
            $return .= "{$parsedUrl['scheme']}:";
        }

        if (!empty($parsedUrl['user']) || !empty($parsedUrl['host'])) {
            $return .= '//';
        }

        if (!empty($parsedUrl['user'])) {
            $return .= $parsedUrl['user'];
            if (!empty($parsedUrl['pass'])) {
                $return .= ":{$parsedUrl['pass']}";
            }
        }

        if (!empty($parsedUrl['host'])) {
            if (!empty($parsedUrl['user'])) {
                $return .= '@';
            }
            $return .= $parsedUrl['host'];
            if (!empty($parsedUrl['port'])) {
                $return .= ":{$parsedUrl['port']}";
            }
        }

        if (!empty($parsedUrl['path'])) {
            $return .= '/' . ltrim($parsedUrl['path'], '/');
        }

        if (!empty($parsedUrl['query'])) {
            $return .= "?{$parsedUrl['query']}";
        }

        if (!empty($parsedUrl['fragment'])) {
            $return .= "#{$parsedUrl['fragment']}";
        }

        return $return;
    }
}
