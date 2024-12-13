<?php

namespace App\Services;

class TemplateService
{
    /**
     *
     * @param string $template
     * @param array $variables
     * @return string
     */
    public function renderTemplate(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $pattern = '/\{\s*' . preg_quote($key, '/') . '\s*\}/';
            $template = preg_replace($pattern, $value, $template);
        }
        return $template;
    }
}
