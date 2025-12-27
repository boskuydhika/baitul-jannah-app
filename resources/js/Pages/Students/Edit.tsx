import { Head, router, useForm } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { ThemeToggle } from '@/Components/theme-toggle';
import { DatePicker } from '@/Components/ui/date-picker';
import { useIsDark } from '@/hooks/use-is-dark';
import { ArrowLeft, Save, User, Trash2, Info, Calendar, Users } from 'lucide-react';
import { cn } from '@/lib/utils';
import { useMemo, useState } from 'react';

// Calculate age from birth date
const calculateAge = (birthDate: string): { years: number; months: number; days: number; formatted: string } | null => {
    if (!birthDate) return null;

    const birth = new Date(birthDate);
    const now = new Date();

    let years = now.getFullYear() - birth.getFullYear();
    let months = now.getMonth() - birth.getMonth();
    let days = now.getDate() - birth.getDate();

    if (days < 0) {
        months--;
        const prevMonth = new Date(now.getFullYear(), now.getMonth(), 0);
        days += prevMonth.getDate();
    }

    if (months < 0) {
        years--;
        months += 12;
    }

    return {
        years,
        months,
        days,
        formatted: `${years} tahun ${months} bulan ${days} hari`
    };
};

// Helper to format date for input
const formatDateForInput = (date: string | null | undefined): string => {
    if (!date) return '';
    if (/^\d{4}-\d{2}-\d{2}$/.test(date)) return date;
    try {
        const parsed = new Date(date);
        if (isNaN(parsed.getTime())) return '';
        return parsed.toISOString().split('T')[0];
    } catch {
        return '';
    }
};

interface Student {
    id: number;
    nis: string;
    name: string;
    nickname: string | null;
    type: string;
    class_time: string;
    gender: string;
    birth_date: string | null;
    birth_place: string | null;
    address: string | null;
    father_name: string | null;
    father_occupation: string | null;
    father_phone: string | null;
    father_wa: string | null;
    mother_name: string | null;
    mother_occupation: string | null;
    mother_phone: string | null;
    mother_wa: string | null;
    registration_date: string | null;
    entry_year: number;
    monthly_fee: number;
    is_active: boolean;
}

interface Props {
    student: Student;
}

export default function StudentEdit({ student }: Props) {
    const isDark = useIsDark();
    const [fatherDifferentWa, setFatherDifferentWa] = useState(
        !!(student.father_wa && student.father_wa !== student.father_phone)
    );
    const [motherDifferentWa, setMotherDifferentWa] = useState(
        !!(student.mother_wa && student.mother_wa !== student.mother_phone)
    );

    const { data, setData, put, processing, errors } = useForm({
        name: student.name,
        nickname: student.nickname || '',
        type: student.type,
        class_time: student.class_time,
        gender: student.gender,
        birth_date: formatDateForInput(student.birth_date),
        birth_place: student.birth_place || '',
        address: student.address || '',
        father_name: student.father_name || '',
        father_occupation: student.father_occupation || '',
        father_phone: student.father_phone || '',
        father_wa: student.father_wa || '',
        mother_name: student.mother_name || '',
        mother_occupation: student.mother_occupation || '',
        mother_phone: student.mother_phone || '',
        mother_wa: student.mother_wa || '',
        registration_date: formatDateForInput(student.registration_date),
        monthly_fee: Math.floor(student.monthly_fee).toString(),
        is_active: student.is_active,
    });

    const age = useMemo(() => calculateAge(data.birth_date), [data.birth_date]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('students.update', student.id));
    };

    const handleDeactivate = () => {
        if (confirm('Nonaktifkan santri ini?')) {
            router.delete(route('students.destroy', student.id));
        }
    };

    const formatCurrency = (value: string) => {
        const num = value.replace(/\D/g, '');
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    const handleTypeChange = (newType: string) => {
        setData(data => ({
            ...data,
            type: newType,
            class_time: newType === 'taud' ? 'pagi' : data.class_time,
        }));
    };

    return (
        <>
            <Head title={`Edit ${student.name}`} />

            {/* Background */}
            <div className="fixed inset-0">
                <div className={cn(
                    "absolute inset-0 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100",
                    isDark ? "opacity-0" : "opacity-100"
                )}></div>
                <div className={cn(
                    "absolute inset-0",
                    isDark ? "opacity-100" : "opacity-0"
                )}>
                    <div className="absolute inset-0 bg-gradient-to-br from-gray-950 via-slate-900 to-gray-900"></div>
                </div>
            </div>

            {/* Content */}
            <div className="relative min-h-screen pb-28">
                {/* Header */}
                <header className={cn(
                    "sticky top-0 z-30 backdrop-blur-xl border-b",
                    isDark ? "bg-gray-900/80 border-gray-800/50" : "bg-white/80 border-gray-200/50"
                )}>
                    <div className="max-w-2xl mx-auto px-4">
                        <div className="flex h-14 items-center justify-between">
                            <div className="flex items-center gap-3">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    onClick={() => router.visit(route('students.show', student.id))}
                                    className={cn("h-9 w-9", isDark ? "text-gray-300" : "text-gray-600")}
                                >
                                    <ArrowLeft className="h-5 w-5" />
                                </Button>
                                <div>
                                    <h1 className={cn("text-lg font-bold", isDark ? "text-white" : "text-gray-900")}>
                                        Edit Santri
                                    </h1>
                                    <p className={cn("text-xs font-mono", isDark ? "text-gray-400" : "text-gray-500")}>
                                        {student.nis}
                                    </p>
                                </div>
                            </div>
                            <ThemeToggle />
                        </div>
                    </div>
                </header>

                <main className="max-w-2xl mx-auto py-4 px-4">
                    <form onSubmit={handleSubmit} className="space-y-4">
                        {/* Status Toggle */}
                        <Card className={cn(
                            "border backdrop-blur-sm",
                            isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                        )}>
                            <CardContent className="p-4">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <Label className={cn("font-medium", isDark ? "text-white" : "text-gray-900")}>Status Santri</Label>
                                        <p className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>
                                            {data.is_active ? 'Santri aktif' : 'Santri tidak aktif'}
                                        </p>
                                    </div>
                                    <label className="relative inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked={data.is_active}
                                            onChange={(e) => setData('is_active', e.target.checked)}
                                            className="sr-only peer"
                                        />
                                        <div className="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                    </label>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Program & Kelas */}
                        <Card className={cn(
                            "border backdrop-blur-sm",
                            isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                        )}>
                            <CardHeader className="pb-3">
                                <CardTitle className={cn("text-base", isDark ? "text-white" : "")}>
                                    Program & Kelas
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <Label className={isDark ? "text-gray-300" : ""}>Program</Label>
                                    <div className="grid grid-cols-2 gap-2 mt-2">
                                        <button
                                            type="button"
                                            onClick={() => handleTypeChange('tpq')}
                                            className={cn(
                                                "p-3 rounded-lg border-2 text-center transition-all",
                                                data.type === 'tpq'
                                                    ? "border-green-500 bg-green-500/10"
                                                    : isDark ? "border-gray-700" : "border-gray-200"
                                            )}
                                        >
                                            <span className={cn("font-semibold", data.type === 'tpq' ? "text-green-500" : isDark ? "text-gray-300" : "text-gray-700")}>
                                                TPQ
                                            </span>
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => handleTypeChange('taud')}
                                            className={cn(
                                                "p-3 rounded-lg border-2 text-center transition-all",
                                                data.type === 'taud'
                                                    ? "border-purple-500 bg-purple-500/10"
                                                    : isDark ? "border-gray-700" : "border-gray-200"
                                            )}
                                        >
                                            <span className={cn("font-semibold", data.type === 'taud' ? "text-purple-500" : isDark ? "text-gray-300" : "text-gray-700")}>
                                                TAUD
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                {data.type === 'tpq' && (
                                    <div>
                                        <Label className={isDark ? "text-gray-300" : ""}>Waktu Kelas</Label>
                                        <div className="grid grid-cols-2 gap-2 mt-2">
                                            <button
                                                type="button"
                                                onClick={() => setData('class_time', 'pagi')}
                                                className={cn(
                                                    "p-3 rounded-lg border-2 text-center",
                                                    data.class_time === 'pagi'
                                                        ? "border-orange-500 bg-orange-500/10"
                                                        : isDark ? "border-gray-700" : "border-gray-200"
                                                )}
                                            >
                                                <span className={cn("font-semibold", data.class_time === 'pagi' ? "text-orange-500" : isDark ? "text-gray-300" : "text-gray-700")}>
                                                    ☀️ Pagi (TPQA)
                                                </span>
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => setData('class_time', 'sore')}
                                                className={cn(
                                                    "p-3 rounded-lg border-2 text-center",
                                                    data.class_time === 'sore'
                                                        ? "border-blue-500 bg-blue-500/10"
                                                        : isDark ? "border-gray-700" : "border-gray-200"
                                                )}
                                            >
                                                <span className={cn("font-semibold", data.class_time === 'sore' ? "text-blue-500" : isDark ? "text-gray-300" : "text-gray-700")}>
                                                    🌙 Sore (TPQB)
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                )}

                                {data.type === 'taud' && (
                                    <div className={cn("flex items-start gap-2 p-3 rounded-lg", isDark ? "bg-purple-900/20" : "bg-purple-50")}>
                                        <Info className={cn("h-4 w-4 mt-0.5", isDark ? "text-purple-400" : "text-purple-600")} />
                                        <p className={cn("text-sm", isDark ? "text-purple-300" : "text-purple-700")}>
                                            TAUD hanya tersedia kelas pagi
                                        </p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {/* Data Santri */}
                        <Card className={cn(
                            "border backdrop-blur-sm",
                            isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                        )}>
                            <CardHeader className="pb-3">
                                <CardTitle className={cn("flex items-center gap-2 text-base", isDark ? "text-white" : "")}>
                                    <User className="h-4 w-4" />
                                    Data Santri
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                <div>
                                    <Label className={isDark ? "text-gray-300" : ""}>Nama Lengkap</Label>
                                    <Input
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        className={cn("mt-1", isDark ? "bg-gray-800 border-gray-700 text-white" : "")}
                                    />
                                    {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                                </div>

                                <div>
                                    <Label className={isDark ? "text-gray-300" : ""}>Nama Panggilan</Label>
                                    <Input
                                        value={data.nickname}
                                        onChange={(e) => setData('nickname', e.target.value)}
                                        placeholder="Contoh: Fauzi, Aisyah"
                                        className={cn("mt-1", isDark ? "bg-gray-800 border-gray-700 text-white" : "")}
                                    />
                                </div>

                                <div>
                                    <Label className={isDark ? "text-gray-300" : ""}>Jenis Kelamin</Label>
                                    <div className="grid grid-cols-2 gap-2 mt-1">
                                        <button
                                            type="button"
                                            onClick={() => setData('gender', 'L')}
                                            className={cn(
                                                "py-2 px-3 rounded-lg border text-sm",
                                                data.gender === 'L'
                                                    ? "border-blue-500 bg-blue-500/10 text-blue-500"
                                                    : isDark ? "border-gray-700 text-gray-400" : "border-gray-200 text-gray-600"
                                            )}
                                        >
                                            👦 Laki-laki
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setData('gender', 'P')}
                                            className={cn(
                                                "py-2 px-3 rounded-lg border text-sm",
                                                data.gender === 'P'
                                                    ? "border-pink-500 bg-pink-500/10 text-pink-500"
                                                    : isDark ? "border-gray-700 text-gray-400" : "border-gray-200 text-gray-600"
                                            )}
                                        >
                                            👧 Perempuan
                                        </button>
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <Label className={isDark ? "text-gray-300" : ""}>Tempat Lahir</Label>
                                        <Input
                                            value={data.birth_place}
                                            onChange={(e) => setData('birth_place', e.target.value)}
                                            className={cn("mt-1", isDark ? "bg-gray-800 border-gray-700 text-white" : "")}
                                        />
                                    </div>
                                    <div>
                                        <Label className={isDark ? "text-gray-300" : ""}>Tanggal Lahir</Label>
                                        <div className="mt-1">
                                            <DatePicker
                                                value={data.birth_date}
                                                onChange={(val) => setData('birth_date', val)}
                                                placeholder="YYYY-MM-DD"
                                                minYear={2000}
                                                maxYear={new Date().getFullYear()}
                                            />
                                        </div>
                                    </div>
                                </div>

                                {/* Age Display */}
                                {age && (
                                    <div className={cn(
                                        "flex items-center gap-2 p-3 rounded-lg",
                                        isDark ? "bg-indigo-900/20" : "bg-indigo-50"
                                    )}>
                                        <Calendar className={cn("h-4 w-4", isDark ? "text-indigo-400" : "text-indigo-600")} />
                                        <span className={cn("text-sm font-medium", isDark ? "text-indigo-300" : "text-indigo-700")}>
                                            Usia: {age.formatted}
                                        </span>
                                    </div>
                                )}

                                <div>
                                    <Label className={isDark ? "text-gray-300" : ""}>Alamat</Label>
                                    <Input
                                        value={data.address}
                                        onChange={(e) => setData('address', e.target.value)}
                                        className={cn("mt-1", isDark ? "bg-gray-800 border-gray-700 text-white" : "")}
                                    />
                                </div>
                            </CardContent>
                        </Card>

                        {/* Data Orangtua / Wali */}
                        <Card className={cn(
                            "border backdrop-blur-sm",
                            isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                        )}>
                            <CardHeader className="pb-3">
                                <CardTitle className={cn("flex items-center gap-2 text-base", isDark ? "text-white" : "")}>
                                    <Users className="h-4 w-4" />
                                    Data Orangtua / Wali
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                {/* Data Ayah */}
                                <div className={cn("p-3 rounded-lg border", isDark ? "border-gray-700 bg-gray-800/30" : "border-gray-200 bg-gray-50")}>
                                    <h4 className={cn("font-medium mb-3", isDark ? "text-blue-400" : "text-blue-600")}>👨 Data Ayah</h4>
                                    <div className="space-y-3">
                                        <div className="grid grid-cols-2 gap-3">
                                            <div>
                                                <Label className={cn("text-sm", isDark ? "text-gray-400" : "text-gray-600")}>Nama Ayah</Label>
                                                <Input
                                                    value={data.father_name}
                                                    onChange={(e) => setData('father_name', e.target.value)}
                                                    placeholder="Nama lengkap"
                                                    className={cn("mt-1", isDark ? "bg-gray-800 border-gray-700 text-white" : "")}
                                                />
                                            </div>
                                            <div>
                                                <Label className={cn("text-sm", isDark ? "text-gray-400" : "text-gray-600")}>Pekerjaan</Label>
                                                <Input
                                                    value={data.father_occupation}
                                                    onChange={(e) => setData('father_occupation', e.target.value)}
                                                    placeholder="Pekerjaan"
                                                    className={cn("mt-1", isDark ? "bg-gray-800 border-gray-700 text-white" : "")}
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <Label className={cn("text-sm", isDark ? "text-gray-400" : "text-gray-600")}>No. HP Ayah</Label>
                                            <Input
                                                value={data.father_phone}
                                                onChange={(e) => setData('father_phone', e.target.value)}
                                                placeholder="08xxxxxxxxxx"
                                                className={cn("mt-1", isDark ? "bg-gray-800 border-gray-700 text-white" : "")}
                                            />
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <Label className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>
                                                No. WA berbeda?
                                            </Label>
                                            <label className="relative inline-flex items-center cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    checked={fatherDifferentWa}
                                                    onChange={(e) => {
                                                        setFatherDifferentWa(e.target.checked);
                                                        if (!e.target.checked) setData('father_wa', '');
                                                    }}
                                                    className="sr-only peer"
                                                />
                                                <div className="w-8 h-4 bg-gray-300 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-green-600"></div>
                                            </label>
                                        </div>
                                        {fatherDifferentWa && (
                                            <div>
                                                <Label className={cn("text-sm", isDark ? "text-gray-400" : "text-gray-600")}>No. WA Ayah</Label>
                                                <Input
                                                    value={data.father_wa}
                                                    onChange={(e) => setData('father_wa', e.target.value)}
                                                    placeholder="08xxxxxxxxxx"
                                                    className={cn("mt-1", isDark ? "bg-gray-800 border-gray-700 text-white" : "")}
                                                />
                                            </div>
                                        )}
                                    </div>
                                </div>

                                {/* Data Ibu */}
                                <div className={cn("p-3 rounded-lg border", isDark ? "border-gray-700 bg-gray-800/30" : "border-gray-200 bg-gray-50")}>
                                    <h4 className={cn("font-medium mb-3", isDark ? "text-pink-400" : "text-pink-600")}>👩 Data Ibu</h4>
                                    <div className="space-y-3">
                                        <div className="grid grid-cols-2 gap-3">
                                            <div>
                                                <Label className={cn("text-sm", isDark ? "text-gray-400" : "text-gray-600")}>Nama Ibu</Label>
                                                <Input
                                                    value={data.mother_name}
                                                    onChange={(e) => setData('mother_name', e.target.value)}
                                                    placeholder="Nama lengkap"
                                                    className={cn("mt-1", isDark ? "bg-gray-800 border-gray-700 text-white" : "")}
                                                />
                                            </div>
                                            <div>
                                                <Label className={cn("text-sm", isDark ? "text-gray-400" : "text-gray-600")}>Pekerjaan</Label>
                                                <Input
                                                    value={data.mother_occupation}
                                                    onChange={(e) => setData('mother_occupation', e.target.value)}
                                                    placeholder="Pekerjaan"
                                                    className={cn("mt-1", isDark ? "bg-gray-800 border-gray-700 text-white" : "")}
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <Label className={cn("text-sm", isDark ? "text-gray-400" : "text-gray-600")}>No. HP Ibu</Label>
                                            <Input
                                                value={data.mother_phone}
                                                onChange={(e) => setData('mother_phone', e.target.value)}
                                                placeholder="08xxxxxxxxxx"
                                                className={cn("mt-1", isDark ? "bg-gray-800 border-gray-700 text-white" : "")}
                                            />
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <Label className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>
                                                No. WA berbeda?
                                            </Label>
                                            <label className="relative inline-flex items-center cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    checked={motherDifferentWa}
                                                    onChange={(e) => {
                                                        setMotherDifferentWa(e.target.checked);
                                                        if (!e.target.checked) setData('mother_wa', '');
                                                    }}
                                                    className="sr-only peer"
                                                />
                                                <div className="w-8 h-4 bg-gray-300 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-green-600"></div>
                                            </label>
                                        </div>
                                        {motherDifferentWa && (
                                            <div>
                                                <Label className={cn("text-sm", isDark ? "text-gray-400" : "text-gray-600")}>No. WA Ibu</Label>
                                                <Input
                                                    value={data.mother_wa}
                                                    onChange={(e) => setData('mother_wa', e.target.value)}
                                                    placeholder="08xxxxxxxxxx"
                                                    className={cn("mt-1", isDark ? "bg-gray-800 border-gray-700 text-white" : "")}
                                                />
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Akademik & Pembayaran */}
                        <Card className={cn(
                            "border backdrop-blur-sm",
                            isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                        )}>
                            <CardHeader className="pb-3">
                                <CardTitle className={cn("text-base", isDark ? "text-white" : "")}>
                                    Akademik & Pembayaran
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <Label className={isDark ? "text-gray-300" : ""}>Tanggal Daftar</Label>
                                        <div className="mt-1">
                                            <DatePicker
                                                value={data.registration_date}
                                                onChange={(val) => setData('registration_date', val)}
                                                placeholder="YYYY-MM-DD"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <Label className={isDark ? "text-gray-300" : ""}>Tahun Masuk</Label>
                                        <div className={cn(
                                            "mt-1 px-3 py-2 rounded-md border text-sm font-medium",
                                            isDark ? "bg-gray-800 border-gray-700 text-white" : "bg-gray-50 border-gray-200 text-gray-700"
                                        )}>
                                            {student.entry_year}
                                        </div>
                                        <p className={cn("text-xs mt-1", isDark ? "text-gray-500" : "text-gray-400")}>
                                            Tidak dapat diubah
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <Label className={isDark ? "text-gray-300" : ""}>Iuran Bulanan (SPP)</Label>
                                    <div className="relative mt-1">
                                        <span className={cn(
                                            "absolute left-3 top-1/2 -translate-y-1/2 text-sm",
                                            isDark ? "text-gray-400" : "text-gray-500"
                                        )}>
                                            Rp
                                        </span>
                                        <Input
                                            value={formatCurrency(data.monthly_fee)}
                                            onChange={(e) => setData('monthly_fee', e.target.value.replace(/\D/g, ''))}
                                            className={cn(
                                                "pl-10 text-right font-mono text-lg",
                                                isDark ? "bg-gray-800 border-gray-700 text-white" : ""
                                            )}
                                        />
                                    </div>
                                    {errors.monthly_fee && <p className="text-red-500 text-xs mt-1">{errors.monthly_fee}</p>}
                                </div>
                            </CardContent>
                        </Card>
                    </form>
                </main>

                {/* Fixed Bottom Actions */}
                <div className="fixed bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-white dark:from-gray-900 via-white/90 dark:via-gray-900/90 to-transparent">
                    <div className="max-w-2xl mx-auto flex gap-3">
                        <Button
                            type="button"
                            variant="outline"
                            onClick={handleDeactivate}
                            className={cn(
                                "flex-shrink-0",
                                isDark ? "border-red-800 text-red-400" : "border-red-200 text-red-600"
                            )}
                        >
                            <Trash2 className="h-4 w-4" />
                        </Button>
                        <Button
                            type="submit"
                            disabled={processing}
                            onClick={handleSubmit}
                            className="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white"
                        >
                            <Save className="h-4 w-4 mr-2" />
                            {processing ? 'Menyimpan...' : 'Simpan'}
                        </Button>
                    </div>
                </div>
            </div>
        </>
    );
}
