<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests\PhpParser\Visitor;

use Povils\PHPMND\PhpParser\Visitor\ParentConnectorVisitor;
use Povils\PHPMND\Tests\Fixtures\PhpParser\ParentConnectorSpyVisitor;
use PhpParser\NodeDumper;

class ParentConnectorVisitorTest extends BaseVisitorTest
{
    private const CODE = <<<'PHP'
<?php

$variable_0 = '0';
$variable_1 = '1';
PHP;

    public function testItAttachesTheParentNodesToEachNode(): void
    {
        $spyParentConnectorVisitor = new ParentConnectorSpyVisitor();

        $nodes = $this->traverse(
            $this->parseCode(self::CODE),
            [
                new ParentConnectorVisitor(),
                $spyParentConnectorVisitor,
            ]
        );

        $dumper = new NodeDumper();

        $expected = <<<'STR'
array(
    0: Stmt_Expression(
        expr: Expr_Assign(
            var: Expr_Variable(
                name: variable_0
            )
            expr: Scalar_String(
                value: 0
            )
        )
    )
    1: Stmt_Expression(
        expr: Expr_Assign(
            var: Expr_Variable(
                name: variable_1
            )
            expr: Scalar_String(
                value: 1
            )
        )
    )
)
STR;

        $this->assertSame($expected, $dumper->dump($nodes));

        $expected = <<<'STR'
array(
    0: null
    1: Stmt_Expression(
        expr: Expr_Assign(
            var: Expr_Variable(
                name: variable_0
            )
            expr: Scalar_String(
                value: 0
            )
        )
    )
    2: Expr_Assign(
        var: Expr_Variable(
            name: variable_0
        )
        expr: Scalar_String(
            value: 0
        )
    )
    3: Expr_Assign(
        var: Expr_Variable(
            name: variable_0
        )
        expr: Scalar_String(
            value: 0
        )
    )
    4: null
    5: Stmt_Expression(
        expr: Expr_Assign(
            var: Expr_Variable(
                name: variable_1
            )
            expr: Scalar_String(
                value: 1
            )
        )
    )
    6: Expr_Assign(
        var: Expr_Variable(
            name: variable_1
        )
        expr: Scalar_String(
            value: 1
        )
    )
    7: Expr_Assign(
        var: Expr_Variable(
            name: variable_1
        )
        expr: Scalar_String(
            value: 1
        )
    )
)
STR;

        $this->assertSame($expected, $dumper->dump($spyParentConnectorVisitor->getCollectedNodes()));
    }
}
