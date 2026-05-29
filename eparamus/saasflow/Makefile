EXEC = docker compose exec wordpress

.PHONY: up down init seed reset dry wp logs open admin nuke

# Start containers (theme changes are live via bind mount)
up:
	docker compose up -d

# Stop containers (data preserved in volumes)
down:
	docker compose down

# First-time bootstrap: install WP core + seed all pages
init:
	$(EXEC) bash /saasflow/bin/docker-init.sh

# Re-seed content (upserts — safe to run anytime)
seed:
	$(EXEC) bash /saasflow/bin/seed.sh --wp-path=/var/www/html

# Re-seed with delete+recreate (use when slugs/parents change)
reset:
	$(EXEC) bash /saasflow/bin/seed.sh --wp-path=/var/www/html --reset

# Preview seed without making changes
dry:
	$(EXEC) bash /saasflow/bin/seed.sh --wp-path=/var/www/html --dry-run

# Run any wp-cli command: make wp CMD="post list --post_type=page"
wp:
	$(EXEC) wp --path=/var/www/html --allow-root $(CMD)

# Shell into the wordpress container
shell:
	$(EXEC) bash

logs:
	docker compose logs -f wordpress

open:
	open http://localhost:8080

admin:
	open http://localhost:8080/wp-admin

# Wipe all data and start fresh (destructive)
nuke:
	docker compose down -v
