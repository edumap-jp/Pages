<?php
class AllPagesTest extends CakeTestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All tests');
		$suite->addTestDirectoryRecursive(TESTS . 'Case');
		return $suite;
	}
}
