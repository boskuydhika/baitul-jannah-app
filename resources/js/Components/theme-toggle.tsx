import * as React from "react"
import { Moon, Sun, Monitor, ChevronDown } from "lucide-react"
import { Button } from "@/Components/ui/button"
import { useTheme } from "@/Components/theme-provider"
import { useIsDark } from "@/hooks/use-is-dark"
import { useState, useRef, useEffect } from "react"
import { cn } from "@/lib/utils"

export function ThemeToggle() {
    const { theme, setTheme } = useTheme()
    const isDark = useIsDark()
    const [isOpen, setIsOpen] = useState(false)
    const dropdownRef = useRef<HTMLDivElement>(null)

    useEffect(() => {
        function handleClickOutside(event: MouseEvent) {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
                setIsOpen(false)
            }
        }
        document.addEventListener('mousedown', handleClickOutside)
        return () => document.removeEventListener('mousedown', handleClickOutside)
    }, [])

    const options = [
        { value: 'light', label: 'Mode Terang', icon: Sun, emoji: '☀️' },
        { value: 'dark', label: 'Mode Gelap', icon: Moon, emoji: '🌙' },
        { value: 'system', label: 'Ikuti Sistem', icon: Monitor, emoji: '💻' },
    ]

    const currentOption = options.find(o => o.value === theme) || options[0]

    return (
        <div className="relative" ref={dropdownRef}>
            <Button
                variant="outline"
                size="sm"
                onClick={() => setIsOpen(!isOpen)}
                className={cn(
                    "h-9 px-3 gap-2 transition-colors",
                    isDark
                        ? "bg-gray-800 border-gray-600 text-gray-200 hover:bg-gray-700"
                        : "bg-white border-gray-200 text-gray-700 hover:bg-gray-50"
                )}
            >
                <span className="flex items-center gap-1.5">
                    <span>☀️</span>
                    <span className={isDark ? "text-gray-500" : "text-gray-400"}>/</span>
                    <span>🌙</span>
                </span>
                <ChevronDown className={cn(
                    "h-3 w-3 opacity-50 transition-transform",
                    isOpen && "rotate-180"
                )} />
            </Button>

            {isOpen && (
                <div className={cn(
                    "absolute right-0 mt-2 w-48 rounded-xl border shadow-xl z-50 overflow-hidden",
                    isDark
                        ? "bg-gray-800 border-gray-700"
                        : "bg-white border-gray-200"
                )}>
                    <div className="p-1.5">
                        <p className={cn(
                            "px-3 py-1.5 text-xs font-medium uppercase tracking-wide",
                            isDark ? "text-gray-500" : "text-gray-400"
                        )}>
                            Tema Tampilan
                        </p>
                        {options.map((option) => {
                            const isActive = theme === option.value
                            return (
                                <button
                                    key={option.value}
                                    onClick={() => {
                                        setTheme(option.value as 'light' | 'dark' | 'system')
                                        setIsOpen(false)
                                    }}
                                    className={cn(
                                        "w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-all",
                                        isActive
                                            ? isDark
                                                ? "bg-indigo-900/40 text-indigo-400 font-medium"
                                                : "bg-indigo-50 text-indigo-600 font-medium"
                                            : isDark
                                                ? "text-gray-300 hover:bg-gray-700"
                                                : "text-gray-700 hover:bg-gray-100"
                                    )}
                                >
                                    <span className="text-base">{option.emoji}</span>
                                    <span className="flex-1 text-left">{option.label}</span>
                                    {isActive && (
                                        <span className={isDark ? "text-indigo-400" : "text-indigo-500"}>✓</span>
                                    )}
                                </button>
                            )
                        })}
                    </div>
                </div>
            )}
        </div>
    )
}
