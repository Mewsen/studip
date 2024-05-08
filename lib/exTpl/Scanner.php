<?php
/**
 * Scanner.php - template parser lexical scanner
 *
 * Simple wrapper class around the Zend engine's lexical scanner. It
 *  automatically skips whitespace.
 *
 * @copyright 2013  Elmar Ludwig
 * @license GPL2 or any later version
 */
namespace exTpl;

class Scanner
{
    private array $tokens;
    private mixed $token_type;
    private mixed $token_value;

    /**
     * Initializes a new Scanner instance for the given text.
     *
     * @param string $text string to parse
     */
    public function __construct(string $text)
    {
        $this->tokens = token_get_all('<?php ' . $text);
    }

    /**
     * Advances the scanner to the next token and returns its token type.
     * The valid token types are those defined for token_get_all() in the
     * PHP documentation. Returns false when the end of input is reached.
     */
    public function nextToken(): mixed
    {
        do {
            $token = next($this->tokens);
            $key   = key($this->tokens);

            // FIXME this workaround should be dropped
            while (
                $token && $token[0] === T_STRING
                && isset($this->tokens[$key + 2])
                && $this->tokens[++$key] === '-'
                && $this->tokens[++$key][0] === T_STRING
            ) {
                $token[1] .= '-' . $this->tokens[$key][1];
                next($this->tokens);
                next($this->tokens);
            }
        } while (is_array($token) && $token[0] === T_WHITESPACE);

        if (is_string($token) || $token === false) {
            $this->token_type  = $token;
            $this->token_value = null;
        } else {
            $this->token_type = $token[0];

            $this->token_value = match ($token[0]) {
                T_CONSTANT_ENCAPSED_STRING => stripcslashes(substr($token[1], 1, -1)),
                T_DNUMBER => (double) $token[1],
                T_LNUMBER => (int) $token[1],
                default => $token[1],
            };
        }

        return $this->token_type;
    }

    /**
     * Returns the current token type. The valid token types are
     * those defined for token_get_all() in the PHP documentation.
     */
    public function tokenType(): mixed
    {
        return $this->token_type;
    }

    /**
     * Returns the current token value if the token type supports
     * a value (T_STRING, T_LNUMBER etc.). Returns null otherwise.
     */
    public function tokenValue(): mixed
    {
        return $this->token_value;
    }
}
