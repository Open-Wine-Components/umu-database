#!/bin/bash
# This script will remove a specific cron job

# Define the cron job to be removed
CRONJOB="$(which python3) "$PWD"/umu_import.py"

# Get the list of current cron jobs
CURRENT_CRONS=$(crontab -l)

# Filter out the cron job to be removed
NEW_CRONS=$(echo "$CURRENT_CRONS" | grep -v "$CRONJOB")

# Update the root user's crontab
echo "$NEW_CRONS" | crontab -

# Print the updated list of cron jobs to confirm the removal
crontab -l
