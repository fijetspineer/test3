# Decompiled Source Code

This directory contains the decompiled source code extracted from the binary files in this repository.

## Binary Files

### SCPSL.exe
- **Type**: PE32+ executable (GUI) x86-64, Windows
- **Architecture**: AMD64 (x86-64)
- **Compiler**: MSVC (linker version 14.44)
- **Language**: C
- **Compiled**: Tue Feb 10 06:04:38 2026
- **Protection**: Themida (anti-tamper/obfuscation)
- **Image Base**: 0x140000000
- **Subsystem**: Windows GUI

#### Exports
| Ordinal | Name |
|---------|------|
| 1 | `AmdPowerXpressRequestHighPerformance` |
| 2 | `D3D12SDKPath` |
| 3 | `D3D12SDKVersion` |
| 4 | `NvOptimusEnablement` |

#### Imports
| Library | Function |
|---------|----------|
| kernel32.dll | GetModuleHandleA |
| api-ms-win-core-synch-l1-2-0.dll | WakeByAddressAll |
| IPHLPAPI.DLL | GetIpNetTable2 |
| ws2_32 | Ordinal_116 |
| ntdll.dll | RtlVirtualUnwind |
| CRYPT32.dll | CryptMsgGetParam |
| SHLWAPI.dll | PathFileExistsW |
| USER32.dll | MessageBoxW |
| ADVAPI32.dll | ReportEventW |
| SHELL32.dll | SHFileOperationW |
| ole32.dll | CoSetProxyBlanket |
| oleaut32 | Ordinal_7 |

### SL-AC.dll (Anti-Cheat Library)
- **Type**: PE32+ DLL (GUI) x86-64, Windows
- **Architecture**: AMD64 (x86-64)
- **Compiler**: MSVC (linker version 14.44)
- **Language**: C
- **Compiled**: Tue Feb 10 06:02:04 2026
- **Protection**: Themida (anti-tamper/obfuscation)
- **Image Base**: 0x180000000
- **Subsystem**: Windows GUI

#### Exports
| Ordinal | Name |
|---------|------|
| 1 | `FreeLpString` |
| 2 | `GetConfig` |
| 3 | `PollAsync` |
| 4 | `SendRequest` |
| 5 | `SendRequestAsync` |

#### Imports
| Library | Function |
|---------|----------|
| kernel32.dll | GetModuleHandleA |
| api-ms-win-core-synch-l1-2-0.dll | WaitOnAddress |
| WINHTTP.dll | WinHttpGetIEProxyConfigForCurrentUser |
| ws2_32 | Ordinal_115 |
| ntdll.dll | RtlVirtualUnwind |
| CRYPT32.dll | CertGetIntendedKeyUsage |
| SHLWAPI.dll | PathFileExistsW |
| USER32.dll | EnumWindows |
| ADVAPI32.dll | CryptGetUserKey |
| SHELL32.dll | SHGetFolderPathW |
| Normaliz.dll | IdnToAscii |
| IPHLPAPI.DLL | if_nametoindex |
| Secur32.dll | InitSecurityInterfaceW |

## Directory Structure

```
decompiled/
├── README.md                           # This file
├── SCPSL/
│   ├── SCPSL_exe_decompiled.c          # Decompiled C pseudocode (199 functions)
│   └── strings.txt                     # Extracted strings (24,273 entries)
└── SL-AC/
    ├── SL-AC_dll_decompiled.c          # Decompiled C pseudocode (55 functions)
    └── strings.txt                     # Extracted strings (17,138 entries)
```

## Decompilation Notes

- **Tool used**: radare2 5.5.0 (`pdc` pseudo-decompiler)
- Both binaries are **Themida-protected**, meaning the majority of code sections are encrypted and virtualized at runtime. Only functions that radare2 could identify in non-protected regions have been decompiled.
- The decompiled pseudocode is **not compilable C** — it is a human-readable representation of the assembly instructions to help understand the program logic.
- The import/export tables listed above show only the functions visible through the PE import address table. Additional API calls are resolved at runtime by Themida's virtual machine and are not visible in static analysis.
- Strings extracted may include data from various sections and may contain noise from encrypted regions.
