<?php
declare(strict_types = 1);

namespace Attributes;

use Waldo\Quux\Blade;

#[AttributeEntity] // disallowed, no $repositoryClass parameter specified
class ClassWithAttributes
{

	#[AttributeEntity] // disallowed
	private const MAYO = true;

	#[AttributeEntity] // disallowed
	public $cheddar = 'plz';

	#[AttributeEntity] // disallowed
	public static $pepper = 'ofc';


	#[AttributeEntity(repositoryClass: UserRepository::class, readOnly: false)] // disallowed, $repositoryClass present with any value
	public function hasAvocado(): bool
	{
	}


	#[AttributeEntity(UserRepository::class)] // allowed, $repositoryClass present with any value
	public function hasTuna(): bool
	{
	}


	#[AttributeEntity(Blade::class)] // allowed, $repositoryClass present with any value
	public function hasKetchup(): bool
	{
	}


	#[AttributeClass()] // disallowed
	public function hasPineapple(
		#[AttributeEntity] // disallowed
		bool $really
	): bool {
	}

}
