import * as React from "react";
import { format, parse, isValid } from "date-fns";
import { id } from "date-fns/locale";
import { Calendar as CalendarIcon, ChevronLeft, ChevronRight } from "lucide-react";
import { cn } from "@/lib/utils";
import { Button } from "@/Components/ui/button";
import { Calendar } from "@/components/ui/calendar";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";
import { Input } from "@/Components/ui/input";

interface DatePickerProps {
    value: string; // YYYY-MM-DD format
    onChange: (value: string) => void;
    placeholder?: string;
    className?: string;
    disabled?: boolean;
    minYear?: number;
    maxYear?: number;
}

export function DatePicker({
    value,
    onChange,
    placeholder = "Pilih tanggal",
    className,
    disabled = false,
    minYear = 1990,
    maxYear = new Date().getFullYear() + 5,
}: DatePickerProps) {
    const [open, setOpen] = React.useState(false);
    const [inputValue, setInputValue] = React.useState(value || "");
    const [viewYear, setViewYear] = React.useState<number>(
        value ? parseInt(value.substring(0, 4)) : new Date().getFullYear()
    );
    const [viewMonth, setViewMonth] = React.useState<number>(
        value ? parseInt(value.substring(5, 7)) - 1 : new Date().getMonth()
    );

    // Sync input value when external value changes
    React.useEffect(() => {
        setInputValue(value || "");
        if (value) {
            setViewYear(parseInt(value.substring(0, 4)));
            setViewMonth(parseInt(value.substring(5, 7)) - 1);
        }
    }, [value]);

    // Parse value to Date
    const date = React.useMemo(() => {
        if (!value) return undefined;
        const parsed = parse(value, "yyyy-MM-dd", new Date());
        return isValid(parsed) ? parsed : undefined;
    }, [value]);

    // Auto-format date with dashes (YYYY-MM-DD)
    const formatWithDashes = (input: string): string => {
        // Remove all non-digits
        const digits = input.replace(/\D/g, '');

        // Build formatted string
        let formatted = '';

        if (digits.length > 0) {
            // Year part (max 4 digits)
            formatted = digits.substring(0, Math.min(4, digits.length));
        }

        if (digits.length > 4) {
            // Add dash and month part (max 2 digits)
            formatted += '-' + digits.substring(4, Math.min(6, digits.length));
        } else if (digits.length === 4 && input.endsWith('-')) {
            // User typed dash after year
            formatted += '-';
        }

        if (digits.length > 6) {
            // Add dash and day part (max 2 digits)
            formatted += '-' + digits.substring(6, Math.min(8, digits.length));
        } else if (digits.length === 6 && input.endsWith('-')) {
            // User typed dash after month
            formatted += '-';
        }

        return formatted;
    };

    // Handle input change (manual typing with auto-format)
    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const rawValue = e.target.value;

        // Allow backspace/delete to work naturally
        if (rawValue.length < inputValue.length) {
            setInputValue(rawValue);
            // If cleared, also clear the value
            if (rawValue === '') {
                onChange('');
            }
            return;
        }

        // Auto-format with dashes
        const formatted = formatWithDashes(rawValue);
        setInputValue(formatted);

        // Try to parse complete YYYY-MM-DD
        if (/^\d{4}-\d{2}-\d{2}$/.test(formatted)) {
            const parsed = parse(formatted, "yyyy-MM-dd", new Date());
            if (isValid(parsed)) {
                onChange(formatted);
                setViewYear(parsed.getFullYear());
                setViewMonth(parsed.getMonth());
            }
        }
    };

    // Handle calendar select
    const handleSelect = (selectedDate: Date | undefined) => {
        if (selectedDate) {
            const formatted = format(selectedDate, "yyyy-MM-dd");
            setInputValue(formatted);
            onChange(formatted);
            setOpen(false);
        }
    };

    // Generate year options
    const yearOptions = React.useMemo(() => {
        const years = [];
        for (let y = maxYear; y >= minYear; y--) {
            years.push(y);
        }
        return years;
    }, [minYear, maxYear]);

    // Month names in Indonesian
    const monthNames = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];

    // Navigate month
    const goToPrevMonth = () => {
        if (viewMonth === 0) {
            setViewMonth(11);
            setViewYear(viewYear - 1);
        } else {
            setViewMonth(viewMonth - 1);
        }
    };

    const goToNextMonth = () => {
        if (viewMonth === 11) {
            setViewMonth(0);
            setViewYear(viewYear + 1);
        } else {
            setViewMonth(viewMonth + 1);
        }
    };

    return (
        <div className={cn("relative", className)}>
            <Popover open={open} onOpenChange={setOpen}>
                <div className="relative">
                    <Input
                        type="text"
                        value={inputValue}
                        onChange={handleInputChange}
                        placeholder={placeholder}
                        disabled={disabled}
                        className="pr-10"
                    />
                    <PopoverTrigger asChild>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            disabled={disabled}
                            className="absolute right-0 top-0 h-full px-3 hover:bg-transparent"
                        >
                            <CalendarIcon className="h-4 w-4 opacity-50" />
                        </Button>
                    </PopoverTrigger>
                </div>
                <PopoverContent className="w-auto p-0" align="start">
                    {/* Custom Header with Year/Month Dropdowns */}
                    <div className="flex items-center justify-between gap-2 p-3 border-b">
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            className="h-7 w-7"
                            onClick={goToPrevMonth}
                        >
                            <ChevronLeft className="h-4 w-4" />
                        </Button>
                        <div className="flex items-center gap-2">
                            <select
                                value={viewYear}
                                onChange={(e) => setViewYear(parseInt(e.target.value))}
                                className="px-2 py-1 text-sm border rounded-md bg-background"
                            >
                                {yearOptions.map((y) => (
                                    <option key={y} value={y}>{y}</option>
                                ))}
                            </select>
                            <select
                                value={viewMonth}
                                onChange={(e) => setViewMonth(parseInt(e.target.value))}
                                className="px-2 py-1 text-sm border rounded-md bg-background"
                            >
                                {monthNames.map((m, i) => (
                                    <option key={m} value={i}>{m}</option>
                                ))}
                            </select>
                        </div>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            className="h-7 w-7"
                            onClick={goToNextMonth}
                        >
                            <ChevronRight className="h-4 w-4" />
                        </Button>
                    </div>
                    <Calendar
                        mode="single"
                        selected={date}
                        onSelect={handleSelect}
                        month={new Date(viewYear, viewMonth)}
                        onMonthChange={(newMonth) => {
                            setViewYear(newMonth.getFullYear());
                            setViewMonth(newMonth.getMonth());
                        }}
                        locale={id}
                        initialFocus
                        className="rounded-md"
                    />
                    {/* Quick Actions */}
                    <div className="flex justify-between p-2 border-t gap-2">
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            className="text-xs"
                            onClick={() => {
                                const today = format(new Date(), "yyyy-MM-dd");
                                setInputValue(today);
                                onChange(today);
                                setOpen(false);
                            }}
                        >
                            Hari Ini
                        </Button>
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            className="text-xs text-destructive"
                            onClick={() => {
                                setInputValue("");
                                onChange("");
                                setOpen(false);
                            }}
                        >
                            Hapus
                        </Button>
                    </div>
                </PopoverContent>
            </Popover>
            {/* Display formatted date */}
            {date && (
                <p className="text-xs text-muted-foreground mt-1">
                    {format(date, "EEEE, d MMMM yyyy", { locale: id })}
                </p>
            )}
        </div>
    );
}
