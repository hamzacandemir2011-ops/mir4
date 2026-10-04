import os
from dotenv import load_dotenv

# Discord IDs can be overridden in .env so the bot can run on another server.
# Defaults are the original Mir4Tools server values.
load_dotenv()


def _id(name: str, default: int) -> int:
    return int(os.getenv(name, default))


GUILD_ID = _id("GUILD_ID", 1127618095687671909)
ADMIN_CHANNEL = _id("ADMIN_CHANNEL", 1141774925552689153)
ROLES_CHANNEL = _id("ROLES_CHANNEL", 1129159066086801578)
ROLES_MESSAGE = _id("ROLES_MESSAGE", 1140727328322879608)

ROLE_EN = _id("ROLE_EN", 1129148272309702756)
ROLE_PT = _id("ROLE_PT", 1129148360591425596)
ROLE_ANNOUNCEMENTS = _id("ROLE_ANNOUNCEMENTS", 1129148508721647657)

INVENTORY_CHANNEL_EN = _id("INVENTORY_CHANNEL_EN", 1128338314877997177)
INVENTORY_CHANNEL_PT = _id("INVENTORY_CHANNEL_PT", 1129154483268632636)
