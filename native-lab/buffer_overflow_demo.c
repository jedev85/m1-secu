#include <stdio.h>
#include <string.h>

int main(int argc, char **argv) {
    char name[16];

    if (argc < 2) {
        printf("usage: %s <name>\n", argv[0]);
        return 1;
    }

    strcpy(name, argv[1]);
    printf("Bonjour %s\n", name);
    return 0;
}
