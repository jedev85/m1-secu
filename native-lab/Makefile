CC ?= cc
CFLAGS ?= -Wall -Wextra -O0 -g

.PHONY: all clean asan fuzz-smoke

all: buffer_overflow_demo parser_fuzz_target

buffer_overflow_demo: buffer_overflow_demo.c
	$(CC) $(CFLAGS) -o $@ $<

parser_fuzz_target: parser_fuzz_target.c
	$(CC) $(CFLAGS) -o $@ $<

asan:
	$(MAKE) clean
	$(MAKE) CFLAGS="-Wall -Wextra -O0 -g -fsanitize=address,undefined"

fuzz-smoke: parser_fuzz_target
	printf "EVT:hello" | ./parser_fuzz_target || true
	printf "BAD" | ./parser_fuzz_target || true
	printf "EVT:%080d" 1 | ./parser_fuzz_target || true

clean:
	rm -f buffer_overflow_demo parser_fuzz_target
