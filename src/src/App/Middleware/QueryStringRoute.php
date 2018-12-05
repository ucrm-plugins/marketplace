<?php
declare(strict_types=1);

namespace App\Middleware;

class QueryStringRoute
{
    public const ROUTE_TYPE_UNKNOWN     = 0;
    public const ROUTE_TYPE_ASSET       = 1;
    public const ROUTE_TYPE_TEMPLATE    = 2;
    public const ROUTE_TYPE_ROUTE       = 4;

    /** @var string */
    protected $directory;

    /** @var string */
    protected $filename;

    /** @var string */
    protected $extension;

    /** @var array */
    protected $query;

    /** @var string */
    protected $original;

    /** @var int */
    protected $type;



    public function __construct(string $directory, string $filename, string $extension, array $query = [])
    {
        $this->directory = $directory;
        $this->filename = $filename;
        $this->extension = $extension;
        $this->query = $query;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getQuery(): array
    {
        return $this->query;
    }


    public function getUrl(): string
    {
        return $this->directory.$this->filename.($this->extension !== "" ? ".$this->extension" : "");
    }

    public function getQueryString(): string
    {
        $query = [];

        foreach($this->query as $name => $value)
            $query[] = "$name=$value";

        $query = implode("&", $query);

        return $query;
    }


    public function getOriginal() : string
    {
        return $this->original;
    }

    public function setOriginal(string $original): QueryStringRoute
    {
        $this->original = $original;
        return $this;
    }

    public function getType() : int
    {
        return $this->type;
    }

    public function setType(int $type): QueryStringRoute
    {
        $this->type = $type;
        return $this;
    }








    public function __toString()
    {
        return json_encode(get_object_vars($this), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }


}