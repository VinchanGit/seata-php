<?php

declare(strict_types=1);
/**
 * Copyright 2019-2022 Seata.io Group.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */
namespace Hyperf\Seata\SqlParser\Antlr\MySql\Listener;

use Hyperf\Seata\SqlParser\Antlr\MySql\Parser\Context;
use Hyperf\Seata\SqlParser\Antlr\MySql\Visit\StatementSqlVisitor;
use Hyperf\Seata\SqlParser\Antlr\MySqlContext;

class SelectSpecificationSqlListener extends MySqlParserBaseListener
{
    private MySqlContext $sqlQueryContext;

    public function __construct(MySqlContext $sqlQueryContext)
    {
        $this->sqlQueryContext = $sqlQueryContext;
    }

    public function enterTableName(Context\TableNameContext $context): void
    {
        $this->sqlQueryContext->setTableName($context->getText());
        parent::enterTableName($context);
    }

    public function enterAtomTableItem(Context\AtomTableItemContext $context): void
    {
        $uid = $context->uid();
        if (! empty($uid)) {
            $text = $uid->getText();
            if (! empty($text)) {
                $this->sqlQueryContext->setTableAlias($text);
            }
        }
        parent::enterAtomTableItem($context); // TODO: Change the autogenerated stub
    }

    public function enterFromClause(Context\FromClauseContext $context): void
    {
        $whereExpr = $context->whereExpr;
        $statementSqlVisitor = new StatementSqlVisitor();
        $text = (string) $statementSqlVisitor->visit($whereExpr);
        $this->sqlQueryContext->setWhereCondition($text);
        parent::enterFromClause($context); // TODO: Change the autogenerated stub
    }

    public function enterFullColumnNameExpressionAtom(Context\FullColumnNameExpressionAtomContext $context): void
    {
        $this->sqlQueryContext->addQueryWhereColumnNames($context->getText());
        parent::enterFullColumnNameExpressionAtom($context); // TODO: Change the autogenerated stub
    }

    public function enterConstantExpressionAtom(Context\ConstantExpressionAtomContext $context): void
    {
        $this->sqlQueryContext->addQueryWhereValColumnNames($context->getText());
        parent::enterConstantExpressionAtom($context); // TODO: Change the autogenerated stub
    }

    public function enterSelectElements(Context\SelectElementsContext $context): void
    {
        $selectElementContexts = $context->selectElement();
        foreach ($selectElementContexts as $elementContext) {
            $this->sqlQueryContext->addQueryColumnNames($elementContext->getText());
        }
        parent::enterSelectElements($context); // TODO: Change the autogenerated stub
    }
}