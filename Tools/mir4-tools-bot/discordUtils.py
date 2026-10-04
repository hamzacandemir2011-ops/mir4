import discord
from config import ROLES_MESSAGE, ROLE_EN, ROLE_PT, ROLE_ANNOUNCEMENTS

async def handleRoleAdd(event: discord.RawReactionActionEvent, user: discord.Member):
    if event.message_id != ROLES_MESSAGE:
        return

    emoji = str(event.emoji)
    if emoji == "🇺🇸":
        await toggleRole(ROLE_EN, user, True)
    if emoji == "🇧🇷":
        await toggleRole(ROLE_PT, user, True)
    if emoji == "📢":
        await toggleRole(ROLE_ANNOUNCEMENTS, user, True)

async def handleRoleRemove(event: discord.RawReactionActionEvent, user: discord.Member):
    if event.message_id != ROLES_MESSAGE:
        return
    
    emoji = str(event.emoji)
    if emoji == "🇺🇸":
        await toggleRole(ROLE_EN, user, False)
    if emoji == "🇧🇷":
        await toggleRole(ROLE_PT, user, False)
    if emoji == "📢":
        await toggleRole(ROLE_ANNOUNCEMENTS, user, False)

async def toggleRole(roleId: int, user: discord.Member, add: bool):
    role = discord.Object(roleId)

    if (add): return await user.add_roles(role)
    else: return await user.remove_roles(role)