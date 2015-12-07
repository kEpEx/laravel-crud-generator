<?php

use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Compilers\CompilerInterface;

class GrudGeneratorBladeCompiler extends BladeCompiler implements CompilerInterface {

	$contentTags = ['%<%', '%>%'];

	
}