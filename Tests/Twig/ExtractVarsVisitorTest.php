<?php

namespace Rj\EmailBundle\Twig;

use Rj\EmailBundle\Twig\ExtractVarsVisitor;

/**
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 */
class ExtractVarsVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractVars()
    {
        $loader = new \Twig_Loader_Array(array());
        $env = new \Twig_Environment($loader);

        $code = 'foo bar {{ test.bar.baz }} {{ foo.bar }} {{ foo2[bar] }} {{ test.bar.qux }}';
        $node = $env->parse($env->tokenize($code));

        $traverser = new \Twig_NodeTraverser($env);
        $visitor = new ExtractVarsVisitor;
        $traverser->addVisitor($visitor);

        $traverser->traverse($node);

        $this->assertSame(array(
            'test' => array(
                'bar' => array(
                    'baz' => array(),
                    'qux' => array(),
                ),
            ),
            'foo' => array(
                'bar' => array(),
            ),
            'foo2' => array(
                '...' => array(),
            ),
        ), $visitor->getExtractedVars());
    }
}

