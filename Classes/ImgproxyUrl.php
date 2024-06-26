<?php

namespace Networkteam\ImageProxy;

/**
 * ImgproxyUrl is a builder instance to build a Url for imgproxy by specifying the source URL, processing options
 * and generating the signature.
 */
class ImgproxyUrl
{
    private ImgproxyBuilder $builder;
    private string $url;
    private ?string $extension = null;

    /**
     * @var string[]
     */
    private $processingOptions = [];

    /**
     * @internal Use ImgproxyBuilder::buildUrl instead
     *
     * @param ImgproxyBuilder $builder
     * @param string $url
     */
    public function __construct(ImgproxyBuilder $builder, string $url)
    {
        $this->builder = $builder;
        $this->url = $url;
    }

    public function extension(?string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    public function resize(?string $resizingType, ?int $width, ?int $height, ?bool $enlarge, ?bool $extend): self
    {
        $opt = 'rs:' .
            ($resizingType ?: '') . ':' .
            ($width != null ? $width : '') . ':' .
            ($height != null ? $height : '') . ':' .
            ($enlarge !== null ? ($enlarge ? 1 : 0) : '') . ':' .
            ($extend !== null ? ($extend ? 1 : 0) : '');
        $this->processingOptions[] = $opt;
        return $this;
    }

    public function fileName(string $path): self
    {
        // Workaround for https://github.com/guzzle/psr7/issues/526
        if (is_numeric($path)) {
            $path = '_' . $path;
        }
        $this->processingOptions[] = 'fn:' . urlencode($path);
        return $this;
    }

    public function build(): string
    {
        return $this->builder->generateUrl($this->url, $this->processingOptions, $this->extension);
    }

    public function quality(int $quality)
    {
        $this->processingOptions[] = 'q:' . $quality;
    }

    public function formatQuality(string $qualityString)
    {
        $this->processingOptions[] = 'fq:' . $qualityString;
    }

    public function cacheBuster(string $cacheBuster)
    {
        $this->processingOptions[] = 'cb:' . $cacheBuster;
    }

    public function focusPoint(float $focusPointX, float $focusPointY)
    {
        $this->processingOptions[] = 'gravity:fp:' . $focusPointX . ':' . $focusPointY;
    }
}
