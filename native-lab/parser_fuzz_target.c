#include <stdio.h>
#include <string.h>

static int parse_event_line(const char *input) {
    char title[32];

    if (strncmp(input, "EVT:", 4) != 0) {
        return 1;
    }

    strcpy(title, input + 4);

    if (strlen(title) == 0) {
        return 2;
    }

    printf("event=%s\n", title);
    return 0;
}

int main(void) {
    char buffer[256];

    if (fgets(buffer, sizeof(buffer), stdin) == NULL) {
        return 1;
    }

    buffer[strcspn(buffer, "\n")] = '\0';
    return parse_event_line(buffer);
}
