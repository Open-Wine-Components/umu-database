#!/bin/bash
# This script will create a cron job

# Define the cron job
CRONJOB="0 * * * * $(which python3) "$PWD"/ulwgl_import.py"

# Add the cron job to the root user's crontab
echo "$CRONJOB" | crontab -

# Print the list of cron jobs to confirm the addition
crontab -l
