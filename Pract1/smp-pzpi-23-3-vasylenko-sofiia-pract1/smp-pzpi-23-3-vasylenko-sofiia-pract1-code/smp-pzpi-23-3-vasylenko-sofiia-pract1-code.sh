#!/bin/bash

if [ $# -ne 2 ]; then
    echo "ПОМИЛКА: Скрипт потребує два аргументи: висота і ширина снігу." >&2
    exit 1
fi

height=$1
snow_width=$2

re='^[0-9]+$'
if ! [[ $height =~ $re ]] || ! [[ $snow_width =~ $re ]] || [ "$height" -le 0 ] || [ "$snow_width" -le 0 ]; then
    echo "ПОМИЛКА: Аргументи повинні бути додатніми цілими числами." >&2
    exit 1
fi

min_total=11
if [ "$height" -lt "$min_total" ]; then
    echo "ПОМИЛКА: Висота повинна бути щонайменше $min_total рядків." >&2
    exit 1
fi

if [ "$snow_width" -lt 9 ] || [ $((snow_width % 2)) -eq 0 ]; then
    echo "ПОМИЛКА: Ширина снігу має бути непарним числом ≥ 9." >&2
    exit 1
fi

echo "OK: Висота=$height, Ширина снігу=$snow_width"

print_branch_layer() {
    local width=$1
    local start_char=$2
    local line_width=1
    local count=0

    until [ "$line_width" -gt "$width" ]; do
        local spaces=$(( (snow_width - line_width) / 2 ))
        printf "%*s" "$spaces" ""
        for ((i=0; i<line_width; i++)); do
            if [ $((i % 2)) -eq 0 ]; then
                printf "%s" "$start_char"
            else
                printf "%s" "$([[ "$start_char" == "*" ]] && echo "#" || echo "*")"
            fi
        done
        echo
        line_width=$((line_width + 2))
        count=$((count + 1))
    done
}

branch_width=$((snow_width - 2))
print_branch_layer "$branch_width" "*"
print_branch_layer "$branch_width" "#"

for i in {1..3}; do
    spaces=$(( (snow_width - 3) / 2 ))
    printf "%*s###\n" "$spaces" ""
done

for ((i=0; i<snow_width; i++)); do
    printf "*"
done
echo
 GitHub репозиторій: https://github.com/NureVasylenkoSofiia/smp-pzpi-23-3-vasylenko-sofiia/blob/main/Pract1/smp-pzpi-23-3-vasylenko-sofiia-pract1/smp-pzpi-23-3-vasylenko-sofiia-pract1-code/smp-pzpi-23-3-vasylenko-sofiia-pract1-code.sh
