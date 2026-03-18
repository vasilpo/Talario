<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

namespace Tygh\Twig;

/**
 * The class that extends the standard Twig class and adds the ability to render a template from a string.
 *
 * @package Tygh\Twig
 */
class TwigEnvironment extends \Twig\Environment
{
    /**
     * Renders a template as string.
     *
     * @param string                                                                                          $string  The template string
     * @param array<string, array|\Tygh\Template\Collection|\Tygh\Template\ITemplate|\Tygh\Template\IContext> $context An array of parameters
     *                                                                                                                      to pass to the template
     *
     * @return string The rendered template
     *
     * @throws \Twig\Error\LoaderError When the template cannot be found.
     * @throws \Twig\Error\SyntaxError When an error occurred during compilation.
     */
    public function renderString($string, array $context = array())
    {
        $template = $this->createTemplate($string);
        return $template->render($context);
    }
}