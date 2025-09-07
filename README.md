![Launcher window](https://github.com/ZzEdovec/truckersmp-linux/raw/refs/heads/main/launcher-window.png)
# ‼️ Deprecated
Use `protontricks-launch` from the protontricks package ([GitHub](https://github.com/Matoking/protontricks), [AUR](https://aur.archlinux.org/packages/protontricks)), which is NOT installed in a sandbox like Flatpak, to launch the original TruckersMP launcher with the Euro Truck Simulator 2 prefix. This launcher is no longer required and its development has been discontinued.

But the story doesn't end! I'm working on a launcher that will combine the launchers of ALL the games that have their own (not Steam and not Epic Games). Stay in touch!

# 🚛 TruckersMP Launcher For Linux
## ❓ What is it?
In fact, this is a graphical wrapper over the [truckersmp-cli](https://github.com/truckersmp-cli/truckersmp-cli).
Dynamic  updates are implemented  here  - the game/mod is updated  only  when  it  really  needs to be updated.  This  means  that  you don't  have to wait a few  minutes  every  time  before  starting the game.
#### ❕ Notice
- This is only the first version of the launcher. It may contain errors and flaws. For example, there is no support for multi language, event tabs, server status, and auto-update of the launcher yet. **If you find a bug or need any features, open the [issue](https://github.com/ZzEdovec/truckersmp-linux/issues).**
- The launcher does not support Flatpak Steam

## ⬇️ Installing
Download latest release from [releases](https://github.com/ZzEdovec/truckersmp-linux/releases) and execute the downloaded file (`chmod +x` may be required)
#### ‼️ Dependency notice
The launcher will not be able to work with the game without the system-wide installed [truckersmp-cli](https://github.com/truckersmp-cli/truckersmp-cli) and the `python-vdf` package.

**You must be logged into your account in the native Steam client**, as the launcher does not support Steam Guard due to the specifics of how SteamCMD operates as subprocess.
