<?php

use EE\Dispatcher\CommandFactory;

require_once dirname( __DIR__ ) . '/php/class-ee-command.php';

class CommandFactoryTests extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider dataProviderExtractLastDocComment
	 */
	function testExtractLastDocComment( $content, $expected ) {
		// Save and set test env var.
		$is_windows = getenv( 'EE_TEST_IS_WINDOWS' );
		putenv( 'EE_TEST_IS_WINDOWS=0' );

		static $extract_last_doc_comment = null;
		if ( null === $extract_last_doc_comment ) {
			$extract_last_doc_comment = new \ReflectionMethod( 'EE\Dispatcher\CommandFactory', 'extract_last_doc_comment' );
			$extract_last_doc_comment->setAccessible( true );
		}

		$actual = $extract_last_doc_comment->invoke( null, $content );
		$this->assertSame( $expected, $actual );

		// Restore.
		putenv( false === $is_windows ? 'EE_TEST_IS_WINDOWS' : "EE_TEST_IS_WINDOWS=$is_windows" );
	}

	/**
	 * @dataProvider dataProviderExtractLastDocComment
	 */
	function testExtractLastDocCommentWin( $content, $expected ) {
		// Save and set test env var.
		$is_windows = getenv( 'EE_TEST_IS_WINDOWS' );
		putenv( 'EE_TEST_IS_WINDOWS=1' );

		static $extract_last_doc_comment = null;
		if ( null === $extract_last_doc_comment ) {
			$extract_last_doc_comment = new \ReflectionMethod( 'EE\Dispatcher\CommandFactory', 'extract_last_doc_comment' );
			$extract_last_doc_comment->setAccessible( true );
		}

		$actual = $extract_last_doc_comment->invoke( null, $content );
		$this->assertSame( $expected, $actual );

		// Restore.
		putenv( false === $is_windows ? 'EE_TEST_IS_WINDOWS' : "EE_TEST_IS_WINDOWS=$is_windows" );
	}

	function dataProviderExtractLastDocComment() {
		return array(
			array( "", false ),
			array( "*/", false ),
			array( "/*/  ", false ),
			array( "/**/", false ),
			array( "/***/ */", false ),
			array( "/***/", "/***/" ),
			array( "\n /**\n  \n  \t\n  */ \t\n \n ", "/**\n  \n  \t\n  */" ),
			array( "\r\n /**\r\n  \r\n  \t\r\n  */ \t\r\n \r\n ", "/**\r\n  \r\n  \t\r\n  */" ),
			array( "/**/ /***/ /***/", "/***/" ),
			array( "asdfasdf/** /** */", "/** /** */" ),
			array( "*//** /** */", "/** /** */" ),
			array( "/** *//** /** */", "/** /** */" ),
			array( "*//** */ /** /** */", "/** /** */" ),
			array( "*//** *//** /** /** */", "/** /** /** */" ),

			array( "/** */class qwer", "/** */" ),
			array( "/**1*/class qwer{}/**2*/class asdf", "/**2*/" ),
			array( "/** */class qwer {}\nclass asdf", false ),
			array( "/** */class qwer {}\r\nclass asdf", false ),

			array( "/** */function qwer", "/** */" ),
			array( "/** */function qwer( \$function ) {}", "/** */" ),
			array( "/**1*/function qwer() {}/**2*/function asdf()", "/**2*/" ),
			array( "/** */function qwer() {}\nfunction asdf()", false ),
			array( "/** */function qwer() {}\r\nfunction asdf()", false ),
			array( "/** */function qwer() {}function asdf()", false ),
			array( "/** */function qwer() {};function asdf( \$function )", false ),
		);
	}

	function testGetDocComment() {
		// Save and set test env var.
		$get_doc_comment = getenv( 'EE_TEST_GET_DOC_COMMENT' );
		$is_windows = getenv( 'EE_TEST_IS_WINDOWS' );

		putenv( 'EE_TEST_GET_DOC_COMMENT=1' );
		putenv( 'EE_TEST_IS_WINDOWS=0' );

		// Make private function accessible.
		$get_doc_comment = new \ReflectionMethod( 'EE\Dispatcher\CommandFactory', 'get_doc_comment' );
		$get_doc_comment->setAccessible( true );

		if ( ! class_exists( 'CommandFactoryTests_Get_Doc_Comment_1_Command', false ) ) {
			require __DIR__ . '/data/commandfactory-doc_comment-class.php';
		}
		if ( ! class_exists( 'CommandFactoryTests_Get_Doc_Comment_1_Command_Win', false ) ) {
			require __DIR__ . '/data/commandfactory-doc_comment-class-win.php';
		}

		// Class 1

		$reflection = new \ReflectionClass( 'CommandFactoryTests_Get_Doc_Comment_1_Command' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 1

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command', 'command1' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 2

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command', 'command2' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 3

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command', 'command3' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 4

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command', 'command4' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );
		$this->assertFalse( $actual );

		// Class 1 Windows

		$reflection = new \ReflectionClass( 'CommandFactoryTests_Get_Doc_Comment_1_Command_Win' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 1

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command_Win', 'command1' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 2

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command_Win', 'command2' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 3

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command_Win', 'command3' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 4

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command_Win', 'command4' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );
		$this->assertFalse( $actual );

		// Class 2

		$reflection = new \ReflectionClass( 'CommandFactoryTests_Get_Doc_Comment_2_Command' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 1

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_2_Command', 'command1' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );
		$this->assertFalse( $actual );

		// Class 2 Windows

		$reflection = new \ReflectionClass( 'CommandFactoryTests_Get_Doc_Comment_2_Command_Win' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 1

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_2_Command_Win', 'command1' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );
		$this->assertFalse( $actual );

		// Functions

		require __DIR__ . '/data/commandfactory-doc_comment-function.php';

		// Function 1

		$reflection = new \ReflectionFunction( 'commandfactorytests_get_doc_comment_func_1' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Function 2

		$reflection = new \ReflectionFunction( 'commandfactorytests_get_doc_comment_func_2' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Function 3

		$reflection = new \ReflectionFunction( $commandfactorytests_get_doc_comment_func_3 );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Restore.
		putenv( false === $get_doc_comment ? 'EE_TEST_GET_DOC_COMMENT' : "EE_TEST_GET_DOC_COMMENT=$get_doc_comment" );
		putenv( false === $is_windows ? 'EE_TEST_IS_WINDOWS' : "EE_TEST_IS_WINDOWS=$is_windows" );
	}

	function testGetDocCommentWin() {
		// Save and set test env var.
		$get_doc_comment = getenv( 'EE_TEST_GET_DOC_COMMENT' );
		$is_windows = getenv( 'EE_TEST_IS_WINDOWS' );

		putenv( 'EE_TEST_GET_DOC_COMMENT=1' );
		putenv( 'EE_TEST_IS_WINDOWS=1' );

		// Make private function accessible.
		$get_doc_comment = new \ReflectionMethod( 'EE\Dispatcher\CommandFactory', 'get_doc_comment' );
		$get_doc_comment->setAccessible( true );

		if ( ! class_exists( 'CommandFactoryTests_Get_Doc_Comment_1_Command', false ) ) {
			require __DIR__ . '/data/commandfactory-doc_comment-class.php';
		}
		if ( ! class_exists( 'CommandFactoryTests_Get_Doc_Comment_1_Command_Win', false ) ) {
			require __DIR__ . '/data/commandfactory-doc_comment-class-win.php';
		}

		// Class 1

		$reflection = new \ReflectionClass( 'CommandFactoryTests_Get_Doc_Comment_1_Command' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 1

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command', 'command1' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 2

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command', 'command2' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 3

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command', 'command3' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 4

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command', 'command4' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );
		$this->assertFalse( $actual );

		// Class 1 Windows

		$reflection = new \ReflectionClass( 'CommandFactoryTests_Get_Doc_Comment_1_Command_Win' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 1

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command_Win', 'command1' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 2

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command_Win', 'command2' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 3

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command_Win', 'command3' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 4

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_1_Command_Win', 'command4' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );
		$this->assertFalse( $actual );

		// Class 2

		$reflection = new \ReflectionClass( 'CommandFactoryTests_Get_Doc_Comment_2_Command' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 1

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_2_Command', 'command1' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );
		$this->assertFalse( $actual );

		// Class 2 Windows

		$reflection = new \ReflectionClass( 'CommandFactoryTests_Get_Doc_Comment_2_Command_Win' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Class method 1

		$reflection = new \ReflectionMethod( 'CommandFactoryTests_Get_Doc_Comment_2_Command_Win', 'command1' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );
		$this->assertFalse( $actual );

		// Functions

		require __DIR__ . '/data/commandfactory-doc_comment-function-win.php';

		// Function 1 Windows

		$reflection = new \ReflectionFunction( 'commandfactorytests_get_doc_comment_func_1_win' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Function 2

		$reflection = new \ReflectionFunction( 'commandfactorytests_get_doc_comment_func_2_win' );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Function 3

		$reflection = new \ReflectionFunction( $commandfactorytests_get_doc_comment_func_3_win );
		$expected = $reflection->getDocComment();

		$actual = $get_doc_comment->invoke( null, $reflection );
		$this->assertSame( $expected, $actual );

		// Restore.
		putenv( false === $get_doc_comment ? 'EE_TEST_GET_DOC_COMMENT' : "EE_TEST_GET_DOC_COMMENT=$get_doc_comment" );
		putenv( false === $is_windows ? 'EE_TEST_IS_WINDOWS' : "EE_TEST_IS_WINDOWS=$is_windows" );
	}
}
