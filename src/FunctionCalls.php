<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;

/**
 * Reports on dynamically calling a disallowed function.
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<FuncCall>
 */
class FunctionCalls implements Rule
{

	/** @var DisallowedHelper */
	private $disallowedHelper;

	/** @var array{function?:string, method?:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>}[] */
	private $forbiddenCalls;


	/**
	 * @param DisallowedHelper $disallowedHelper
	 * @param array{function?:string, method?:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>}[] $forbiddenCalls
	 */
	public function __construct(DisallowedHelper $disallowedHelper, array $forbiddenCalls)
	{
		$this->disallowedHelper = $disallowedHelper;
		$this->forbiddenCalls = $forbiddenCalls;
	}


	public function getNodeType(): string
	{
		return FuncCall::class;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @return string[]
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var FuncCall $node */
		if (!($node->name instanceof Name)) {
			return [];
		}

		$name = $node->name . '()';
		foreach ($this->forbiddenCalls as $forbiddenCall) {
			if (!isset($forbiddenCall['function'])) {
				throw new ShouldNotHappenException("Key 'function' missing in disallowedFunctionCalls configuration");
			}
			if ($name === $forbiddenCall['function'] && !$this->disallowedHelper->isAllowed($scope, $node->args, $forbiddenCall)) {
				return [
					sprintf('Calling %s is forbidden, %s', $name, $forbiddenCall['message'] ?? 'because reasons'),
				];
			}
		}

		return [];
	}
}
