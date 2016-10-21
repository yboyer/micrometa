<?php

namespace YF\Domain;

class Image extends Domain
{
    private $filename;
    private $path;
    private $name;
    private $creator;

    /**
     * Get the value of Filename
     *
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the value of Filename
     *
     * @param mixed filename
     *
     * @return self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get the value of Path
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of Path
     *
     * @param mixed path
     *
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of Name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of Name
     *
     * @param mixed name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of Creator
     *
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set the value of Creator
     *
     * @param mixed creator
     *
     * @return self
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;

        return $this;
    }

}
