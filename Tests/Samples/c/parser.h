#ifndef _PARSER_H_
#define _PARSER_H_

#include "tokens.h"
#include <stdio.h>

#define S_EXPR 1
#define S_JUMP 2
#define S_CJMP 3
#define S_NOPR 4

typedef char  statement_kind_t;
typedef void* any_t;

typedef struct statement {
    statement_kind_t kind;
    int id;
    
    any_t data;
    statement* next;    
} statement_t;

typedef struct {
    int statements;

    statement_t* first;
    statement_t* last;
} statement_list_t;

typedef struct expression {
    _stack_elem* first;
    _stack_elem* last;
} expression_t;

struct conditional_jump_s { 
    expression_t condition;
    statement_t* jump;
};

typedef struct {
    tokenizer_t* tokenizer;
    token_t      last;

    statement_list_t list;
} parser_t;

void expression_push(expression_t* expression, token_t token);
void expression_destroy(expression_t* expression);
void expression_print(expression_t* e, FILE* stream);

statement_t* statement_list_push(statement_list_t* list, statement_kind_t kind, any_t data = 0);
void statement_list_destroy(statement_list_t* list);
void statement_list_print(statement_list_t* list, FILE* stream);

void statement_print(statement_t* statement, FILE* stream);

void parser_init(parser_t* parser, tokenizer_t* tokenizer);
void parser_advance(parser_t* parser);

expression_t parse_expression(parser_t* parser);
void parse_conditional(parser_t* parser);
void parse_statement(parser_t* parser);
statement_list_t parse_all(parser_t* parser);

char operator_get_priority(int kind);
bool operator_is_left(int kind);

#endif
