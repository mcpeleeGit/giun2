#!/bin/bash

LOG_FILE="php_error.log"

# 최근 에러 라인 가져오기
error_line=$(tail -n 20 "$LOG_FILE" | grep -E "Fatal error|Parse error" | tail -n 1)

# 파일 경로와 줄 번호 추출 (grep -P 사용 안함)
file_path=$(echo "$error_line" | sed -n 's/.*in \\(.*\\.php\\).*/\\1/p')
line_number=$(echo "$error_line" | sed -n 's/.*on line \\([0-9]*\\).*/\\1/p')

# Cursor로 열기
if [[ -n "$file_path" && -n "$line_number" ]]; then
    echo "📂 Opening $file_path:$line_number in Cursor..."
    cursor open "$file_path:$line_number"
else
    echo "❌ No valid error found in $LOG_FILE"
fi
