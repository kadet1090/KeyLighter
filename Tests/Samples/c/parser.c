#include "parser.h"
#include <stdlib.h>

char operator_get_priority(int kind) {
    switch(kind) {
        case T_ASSIGN:  return 1;
        case T_OR:      return 2;
        case T_AND:     return 3;
        case T_CMP_EQ:
        case T_CMP_NEQ: return 4;
        case T_CMP_LE:
        case T_CMP_GE:
        case T_CMP_LEQ:
        case T_CMP_GEQ: return 5;
        case T_ADD:
        case T_SUB:     return 6;
        case T_DIV:
        case T_MUL:
        case T_MOD:     return 7;
        case T_NEG:
        case T_MINUS_U: return 8;
    }

    return -1;
}

bool operator_is_left(int kind) {
	return kind != T_ASSIGN && kind != T_NEG && kind != T_MINUS_U;
}

void expression_push(expression_t* expr, token_t token) {
    _stack_elem* elem = (_stack_elem*)malloc(sizeof(_stack_elem));

    elem->next = 0;
    elem->token = token;

    if(expr->first == 0) {
        expr->first = elem;
        expr->last  = elem;
        return;
    }

    expr->last->next = elem;
    expr->last = elem;
}

void expression_destroy(expression_t* expr)
{
	while(expr->first) {
		_stack_elem* tmp = expr->first;
		expr->first = expr->first->next;

        if(tmp->token.kind == T_VAR) free(tmp->token.value.str);

		free(tmp);
	}
}

inline bool should_pop(token_t* op1, token_t* op2) {
    return ( operator_is_left(op1->kind) && operator_get_priority(op1->kind) <= operator_get_priority(op2->kind))
        || (!operator_is_left(op1->kind) && operator_get_priority(op1->kind) <  operator_get_priority(op2->kind));
}

void parser_advance(parser_t* parser) {
     parser->last = tokenizer_next(parser->tokenizer, &parser->last);
}

void parser_init(parser_t* parser, tokenizer_t* tokenizer)
{
    parser->tokenizer = tokenizer;
    parser->last = tokenizer_next(tokenizer, 0);

    parser->list.last       = parser->list.first = 0;
	parser->list.statements = 0;
}

void handle_tokens(parser_t* parser, stack_t* operators, expression_t* expr) {
    token_t last = { T_L_PAREN };
    token_t* current = &parser->last;

    int parens = 0;

    do {
        switch(current->kind) {
            case T_VAR:
            case T_LITERAL:
                // Jezeli zmienna nastepuje po zmiennej - to cos poszlo grubo nie tak, a na powaznie to po prostu 
                // zaczelo sie nowe wyrazenie.
                if (!token_is_operator(&last) && last.kind != T_L_PAREN && last.kind != T_L_BRACE) return;

                expression_push(expr, *current);
                break;
            case T_L_PAREN:
                parens++;
                if (!token_is_operator(&last) && last.kind != T_L_PAREN && last.kind != T_L_BRACE) return;
                
                stack_push(operators, *current);
                break;
            case T_R_PAREN:
                if(!parens) return;

                while(!stack_empty(operators) && operators->current->token.kind != T_L_PAREN)
                    expression_push(expr, stack_pop(operators));
                
                parens--;

                if(stack_empty(operators)) {
                    fprintf(stderr, "Cos poszlo nie tak z nawiasami");
                    break;
                }

                stack_pop(operators);
                break;

             case T_L_BRACE:
             case T_R_BRACE:
             case T_IF:
             case T_WHILE:
                return;
        }

        if(token_is_operator(current)) {
            while(!stack_empty(operators) && should_pop(current, &operators->current->token))       
                expression_push(expr, stack_pop(operators));
            
            stack_push(operators, *current);
        }

        last = *current;
        parser_advance(parser);
    } while(current->kind != T_EOF);
}

statement_t* statement_alloc(statement_kind_t kind, any_t data = 0) 
{
    statement_t* result = (statement_t*)malloc(sizeof(statement_t));
    result->kind = kind;
    result->data = data;

    return result;
}

#define EXPECTED(token) if(parser->last.kind != token) { \
    char tn[10]; \
    kind_to_str(parser->last.kind, tn); \
    fprintf(stderr, "Expected " #token ", got %s.\n", tn); \
    return; \
}

void parse_conditional(parser_t* parser) 
{
    int kind = parser->last.kind;
        
    conditional_jump_s* cjmps = (conditional_jump_s*)malloc(sizeof(conditional_jump_s));
    
    parser_advance(parser);
    EXPECTED(T_L_PAREN);
    parser_advance(parser);
    
    cjmps->condition = parse_expression(parser);
    
    EXPECTED(T_R_PAREN);
    parser_advance(parser);
    EXPECTED(T_L_BRACE);
    parser_advance(parser);

    statement_t* statement = statement_list_push(&parser->list, S_CJMP, cjmps);

    while(parser->last.kind != T_R_BRACE) parse_statement(parser);
    if(kind == T_WHILE) statement_list_push(&parser->list, S_JUMP, statement);

    statement_t* noop = statement_list_push(&parser->list, S_NOPR);
    cjmps->jump = noop;
   
    parser_advance(parser);
}

void parse_statement(parser_t* parser) {
    if(parser->last.kind == T_IF || parser->last.kind == T_WHILE) {
        parse_conditional(parser);
        return; 
    }

    expression_t* expr = (expression_t*)malloc(sizeof(expression_t));
    *expr = parse_expression(parser);

    statement_list_push(&parser->list, S_EXPR, expr);
}

statement_list_t parse_all(parser_t* parser) {
    while(parser->last.kind != T_EOF) {
        parse_statement(parser);
    }
    return parser->list;
}

expression_t parse_expression(parser_t* parser)
{
    stack_t operators   = {  }; 
    expression_t output = {  };
 
    handle_tokens(parser, &operators, &output);
    while(!stack_empty(&operators)) expression_push(&output, stack_pop(&operators));

    return output;
}

void expression_print(expression_t* expr, FILE* stream) {
    _stack_elem* current = expr->first;
    while(current) {
        switch(current->token.kind) {
            case T_LITERAL: fprintf(stream, "%d ", current->token.value.val.value); break;
            case T_VAR:     fprintf(stream, "%s ", current->token.value.str); break;
            case T_ADD:     fprintf(stream, "+ "); break;
            case T_SUB:     fprintf(stream, "- "); break;
            case T_MUL:     fprintf(stream, "* "); break;
            case T_DIV:     fprintf(stream, "/ "); break;
            case T_MOD:     fprintf(stream, "%% "); break;
            case T_CMP_EQ:  fprintf(stream, "== "); break;
            case T_CMP_NEQ: fprintf(stream, "!= "); break;
            case T_CMP_LE:  fprintf(stream, "< "); break;
            case T_CMP_GE:  fprintf(stream, "> "); break;
            case T_CMP_LEQ: fprintf(stream, "<= "); break;
            case T_CMP_GEQ: fprintf(stream, ">= "); break;
            case T_MINUS_U: fprintf(stream, "- "); break;
            case T_NEG:     fprintf(stream, "! "); break;
            case T_AND:     fprintf(stream, "& "); break;
            case T_OR:      fprintf(stream, "| "); break;
            case T_ASSIGN:  fprintf(stream, "= "); break;
        }

        current = current->next;
    }
}

statement_t* statement_list_push(statement_list_t* list, statement_kind_t kind, any_t data) 
{
    statement_t* statement = (statement_t*)malloc(sizeof(statement_t));
    statement->kind = kind;
    statement->data = data;
    statement->id   = list->statements++;
    statement->next = 0;

    if(list->first == 0) {
        list->first = list->last = statement;
    } else {
        list->last->next = statement;
        list->last = statement;
    }

    return statement;
}

void statement_list_destroy(statement_list_t* list)
{
	statement_t* iter = list->first;
	while (iter) {
		if (iter->kind == S_CJMP) {
			expression_destroy(&((conditional_jump_s*)iter->data)->condition);
			free(iter->data);
		} else if(iter->kind == S_EXPR) {
			expression_destroy((expression_t*)iter->data);
			free(iter->data);
		}

		statement_t* tmp = iter;
		iter = iter->next;

		free(tmp);
	}
}

void statement_print(statement_t* statement, FILE* stream) {
    fprintf(stream, "#%03d: ", statement->id);

    switch(statement->kind) {
        case S_NOPR:
            fprintf(stream, "NOPR");
            break;
        case S_JUMP:
            fprintf(stream, "JUMP #%03d", ((statement_t*)statement->data)->id);
            break;
        case S_CJMP:
            fprintf(stream, "CJMP #%03d if ", ((conditional_jump_s*)statement->data)->jump->id);
            expression_print(&((conditional_jump_s*)statement->data)->condition, stream);
            break;
        case S_EXPR:
            fprintf(stream, "EXPR ");
            expression_print((expression_t*)statement->data, stream);
            break;
    }
}

void statement_list_print(statement_list_t* list, FILE* stream)
{
    statement_t* iter = list->first;
    while(iter) {
        statement_print(iter, stream);
        fprintf(stream, "\n");

        iter = iter->next;
    }
}
