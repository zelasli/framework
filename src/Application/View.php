<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli\Application
 */

namespace Zelasli\Application;

class View
{
    protected array $data = [];

    protected string $templateDir;

    protected string $templateName;

    public function __construct($name, $templateDir)
    {
        $this->templateName = $name;
        $this->templateDir = $templateDir;
    }

    protected function fetch($view, $data = []): string
    {
        ob_start();
        
        extract(array_merge($this->data, $data));

        include($this->getTemplateFilePath($view));

        return ob_get_clean();
    }

    public function getContent()
    {
        return $this->render($this->templateName);
    }

    protected function getTemplateFilePath($view): string
    {
        $templatePath = rtrim($this->templateDir, '/') . DIRECTORY_SEPARATOR . $view . '.vuin.php';

        if (is_file($templatePath)) {
            return $templatePath;
        }

        throw new MissingTemplateException(
            "View (" . $view . ") not found"
        );
    }
    protected function include($template, $data = [])
    {
        echo $this->fetch($template, $data);
    }
  
    protected function render($view): string
    {
        return $this->fetch($view);
    }

    public function set($data)
    {
        $this->data = array_merge(
            $this->data,
            $data
        );
    }
}
