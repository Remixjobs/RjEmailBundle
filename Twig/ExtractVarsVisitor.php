<?php

namespace Rj\EmailBundle\Twig;

/**
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 */
class ExtractVarsVisitor implements \Twig_NodeVisitorInterface
{
    private $stack;
    private $vars;
    private $currentVar;

    public function __construct()
    {
        $this->stack = [];
        $this->vars = [];
        $this->currentVar = &$this->vars;
    }

    public function enterNode(\Twig_NodeInterface $node, \Twig_Environment $env)
    {
        $this->stack[] = $node;
        return $node;
    }

    public function leaveNode(\Twig_NodeInterface $node, \Twig_Environment $env)
    {
        array_pop($this->stack);

        if ($node instanceof \Twig_Node_Expression_GetAttr) {

            $nameNode = $node->getNode('node');
            if ($nameNode instanceof \Twig_Node_Expression_Name) {
                $this->addVar($nameNode->getAttribute('name'));
            }

            $attributeNode = $node->getNode('attribute');
            if ($attributeNode instanceof \Twig_Node_Expression_Constant) {
                $this->addVar($attributeNode->getAttribute('value'));
            } else {
                $this->addVar('...');
            }

            if (!(end($this->stack) instanceof \Twig_Node_Expression_GetAttr)) {
                $this->resetVars();
            }
        }

        return $node;
    }

    public function getPriority()
    {
        return 0;
    }

    public function getExtractedVars()
    {
        return $this->vars;
    }

    private function addVar($name)
    {
        if (!isset($this->currentVar[$name])) {
            $this->currentVar[$name] = [];
        }
        $this->currentVar = &$this->currentVar[$name];
    }

    private function resetVars()
    {
        $this->currentVar = &$this->vars;
    }
}

