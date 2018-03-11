<?php

namespace Library\Interfaces;

interface ComposerInterface
{
    public function attach(RenderInterface $view);
    public function detach(RenderInterface $view);
}