#!/bin/bash

SCRIPT_VERSION="1.0"

quiet=false
selected_group=""
csv_path=""

ERR_NO_FILE=1
ERR_NO_READ=2
ERR_NO_GROUP=3
ERR_CONVERT_FAIL=4

fail_exit() {
    echo "ПОМИЛКА: $1" >&2
    exit "$2"
}

validate_file() {
    [[ ! -f "$1" ]] && fail_exit "Файл '$1' не знайдено" $ERR_NO_FILE
    [[ ! -r "$1" ]] && fail_exit "Файл '$1' нечитабельний" $ERR_NO_READ
}

usage_info() {
    cat <<EOF
Синтаксис:
  task2 [--help | --version] | [[-q|--quiet] [назва_групи] файл.csv]

Опції:
  --help       Вивід цього повідомлення
  --version    Показ версії
  -q, --quiet  Приховати повідомлення

Аргументи:
  назва_групи    Назва академічної групи (формат ПЗПІ-XX-X)
  файл.csv       Експортований CSV з cist
EOF
    exit 0
}

print_version() {
    echo "Версія: $SCRIPT_VERSION"
    exit 0
}

log() {
    $quiet || echo "$1"
}

choose_csv_file() {
    local files=($(ls -t | grep -E '^TimeTable_.._.._20..\.csv'))

    [[ ${#files[@]} -eq 0 ]] && fail_exit "CSV-файли не знайдено" $ERR_NO_FILE

    log "Список доступних файлів:"
    files+=("ВИХІД")

    select f in "${files[@]}"; do
        if [[ "$f" == "ВИХІД" ]]; then
            exit 0
        elif [[ -n "$f" ]]; then
            csv_path="$f"
            validate_file "$csv_path"
            break
        else
            log "Невірний вибір. Повторіть спробу."
        fi
    done
}

extract_groups() {
    iconv -f cp1251 -t utf-8 "$1" | sed 's/\r/\n/g' | \
    awk 'BEGIN{ FPAT="([^,]*|\"[^\"]*\")" } NR>1 {
        if ($1 ~ /ПЗПІ-[0-9]{2}-[0-9]+/) {
            match($1, /ПЗПІ-[0-9]{2}-[0-9]+/, m)
            if (m[0] != "") print m[0]
        }
    }' | sort -u
}

group_prompt() {
    log "Групи у файлі $csv_path:"
    log "Знайдено ${#all_groups[@]} груп"

    if [[ ${#all_groups[@]} -eq 1 ]]; then
        selected_group="${all_groups[0]}"
        log "Автовибір: $selected_group"
    else
        all_groups+=("ВИХІД")
        select g in "${all_groups[@]}"; do
            if [[ "$g" == "ВИХІД" ]]; then
                exit 0
            elif [[ -n "$g" ]]; then
                selected_group="$g"
                log "Вибрано: $selected_group"
                break
            else
                log "Невірний ввід. Спробуйте знову."
            fi
        done
    fi
}

verify_group() {
    local name="$1"
    for g in "${all_groups[@]}"; do
        [[ "$g" == "$name" ]] && return 0
    done

    log "Група '$name' не знайдена."
    for g in "${all_groups[@]}"; do
        log "- $g"
    done
    return 1
}

convert_schedule() {
    local dt=$(echo "$csv_path" | grep -o '[0-9]\{2\}_[0-9]\{2\}_20[0-9]\{2\}')
    [[ -z "$dt" ]] && fail_exit "Не вдалося визначити дату" $ERR_CONVERT_FAIL

    local out_file="Google_TimeTable_${dt}.csv"
    echo "Subject,Start Date,Start Time,End Date,End Time,Description" > "$out_file"

    log "Обробка для: $selected_group"

    if ! iconv -f cp1251 -t utf-8 "$csv_path" | sed 's/\r/\n/g' | awk -v grp="$selected_group" -v only_one="${#all_groups[@]}" '
    BEGIN {
        FPAT="([^,]*|\"[^\"]*\")"
    }

    function clean(s) {
        gsub(/^"+|"+$/, "", s)
        return s
    }

    function date_us(d) {
        split(clean(d), a, ".")
        return a[2] "/" a[1] "/" a[3]
    }

    function time_us(t) {
        split(clean(t), p, ":")
        h = p[1]+0; m = p[2]
        period = (h >= 12) ? "PM" : "AM"
        h = (h % 12); if (h == 0) h = 12
        return sprintf("%02d:%s %s", h, m, period)
    }

    NR > 1 {
        if (only_one == 1 || $1 ~ grp) {
            subj = clean($1); gsub(/^.* - /, "", subj)

            sdate = date_us($2)
            edate = date_us($4)

            stime = time_us($3)
            etime = time_us($5)

            desc = clean($12)

            key = subj "|" sdate "|" stime "|" edate "|" etime "|" desc "|" $1
            lines[NR] = key
        }
    }
    END {
        for (i in lines) print lines[i]
    }
    ' | sort -t'|' -k2,2 -k3,3 | awk -F'|' '
    BEGIN {
        delete lecs; delete pracs; delete labs
        prev = ""; prev_day = ""; prev_n = 0
    }
    {
        subj = $1; day = $2; start = $3; endd = $4; endt = $5; desc = $6; orig = $7

        name = subj; sub(/ .*/, "", name)

        if (!(name in lecs)) { lecs[name]=0; pracs[name]=0; labs[name]=0 }

        if (subj ~ /Лк/) {
            lecs[name]++
            subj = subj "; №" lecs[name]
        } else if (subj ~ /Пз/) {
            pracs[name]++
            subj = subj "; №" pracs[name]
        } else if (subj ~ /Лб/) {
            if (name == prev && day == prev_day) {
                subj = subj "; №" prev_n
            } else {
                labs[name]++
                subj = subj "; №" labs[name]
                prev_n = labs[name]
            }
            prev = name; prev_day = day
        }

        printf "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n", subj, day, start, endd, endt, desc
    }
    ' >> "$out_file"; then
        fail_exit "Помилка при обробці CSV" $ERR_CONVERT_FAIL
    fi

    log "Файл збережено як $out_file"
}

# --- Аргументи командного рядка ---
while [[ $# -gt 0 ]]; do
    case "$1" in
        --help) usage_info ;;
        --version) print_version ;;
        -q|--quiet) quiet=true; shift ;;
        *)
            [[ -z "$csv_path" && -f "$1" ]] && csv_path="$1"
            [[ -z "$selected_group" && "$1" =~ ^ПЗПІ-[0-9]{2}-[0-9]+$ ]] && selected_group="$1"
            shift ;;
    esac
done

[[ -z "$csv_path" ]] && choose_csv_file || validate_file "$csv_path"
mapfile -t all_groups < <(extract_groups "$csv_path")
[[ ${#all_groups[@]} -eq 0 ]] && fail_exit "Групи не знайдені" $ERR_NO_GROUP

[[ -n "$selected_group" ]] && ! verify_group "$selected_group" && group_prompt
[[ -z "$selected_group" ]] && group_prompt

log "Група: $selected_group"
convert_schedule