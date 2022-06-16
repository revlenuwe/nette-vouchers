<?php


namespace App\Services;


class VoucherGenerator
{
    protected $mask;

    protected $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    protected $separator = '-';

    protected $prefix;

    protected $suffix;

    public function __construct(string $mask = '*****-*****')
    {
        $this->mask = $mask;
    }

    public function setMask(string $mask): self
    {
        $this->mask = $mask;

        return $this;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function setSuffix(string $suffix): self
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function generateCode(): string
    {
        $mask = $this->mask;

        for ($i = 0; $i < strlen($this->mask); $i++) {
            $mask = $this->replaceFirst('*', $this->getRandomCharacter(), $mask);
        }

        return $this->getPrefix() . $mask . $this->getSuffix();
    }

    function replaceFirst($search, $replace, $subject): string
    {
        $pos = strpos($subject, $search);
        if ($pos === false) {
            return $subject;
        }

        return substr($subject, 0, $pos) . $replace . substr($subject, $pos + strlen($search));
    }

    protected function getPrefix(): string
    {
        return $this->prefix ? $this->prefix . $this->separator : '';
    }

    protected function getSuffix(): string
    {
        return $this->suffix ? $this->separator . $this->suffix : '';
    }

    private function getRandomCharacter(): string
    {
        return $this->characters[rand(0, strlen($this->characters) - 1)];
    }

}