import { Head, router } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { ThemeToggle } from '@/Components/theme-toggle';
import { useIsDark } from '@/hooks/use-is-dark';
import {
    ArrowLeft,
    Edit2,
    CreditCard,
    Phone,
    MapPin,
    Calendar,
    User,
    MessageCircle,
    Briefcase,
    Users
} from 'lucide-react';
import { cn } from '@/lib/utils';

interface Student {
    id: number;
    nis: string;
    name: string;
    nickname: string | null;
    type: string;
    type_label: string;
    class_time: string;
    class_label: string;
    gender: string;
    birth_date: string | null;
    birth_place: string | null;
    address: string | null;
    father_name: string | null;
    father_occupation: string | null;
    father_phone: string | null;
    father_wa: string | null;
    father_whatsapp_link: string | null;
    mother_name: string | null;
    mother_occupation: string | null;
    mother_phone: string | null;
    mother_wa: string | null;
    mother_whatsapp_link: string | null;
    registration_date: string | null;
    entry_year: number;
    monthly_fee: number;
    formatted_monthly_fee: string;
    is_active: boolean;
    age: {
        years: number;
        months: number;
        days: number;
        formatted: string;
        short: string;
    } | null;
    primary_whatsapp: string | null;
    primary_whatsapp_link: string | null;
}

interface Props {
    student: Student;
}

export default function StudentShow({ student }: Props) {
    const isDark = useIsDark();

    const getClassColor = () => {
        if (student.type === 'taud') {
            return isDark ? 'text-purple-400' : 'text-purple-600';
        }
        if (student.class_time === 'sore') {
            return isDark ? 'text-blue-400' : 'text-blue-600';
        }
        return isDark ? 'text-orange-400' : 'text-orange-600';
    };

    const getClassBadgeStyle = () => {
        if (student.type === 'taud') {
            return isDark
                ? 'bg-purple-900/30 text-purple-400 border-purple-800'
                : 'bg-purple-100 text-purple-700 border-purple-200';
        }
        if (student.class_time === 'sore') {
            return isDark
                ? 'bg-blue-900/30 text-blue-400 border-blue-800'
                : 'bg-blue-100 text-blue-700 border-blue-200';
        }
        return isDark
            ? 'bg-orange-900/30 text-orange-400 border-orange-800'
            : 'bg-orange-100 text-orange-700 border-orange-200';
    };

    const formatDate = (dateStr: string | null) => {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    };

    return (
        <>
            <Head title={student.name} />

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
                                    onClick={() => router.visit(route('students.index'))}
                                    className={cn("h-9 w-9", isDark ? "text-gray-300" : "text-gray-600")}
                                >
                                    <ArrowLeft className="h-5 w-5" />
                                </Button>
                                <div>
                                    <h1 className={cn("text-lg font-bold", isDark ? "text-white" : "text-gray-900")}>
                                        Detail Santri
                                    </h1>
                                </div>
                            </div>
                            <ThemeToggle />
                        </div>
                    </div>
                </header>

                <main className="max-w-2xl mx-auto py-4 px-4 space-y-4">
                    {/* Profile Card */}
                    <Card className={cn(
                        "border backdrop-blur-sm overflow-hidden",
                        isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                    )}>
                        <div className={cn(
                            "h-16",
                            student.type === 'taud'
                                ? "bg-gradient-to-r from-purple-600 to-purple-400"
                                : student.class_time === 'sore'
                                    ? "bg-gradient-to-r from-blue-600 to-blue-400"
                                    : "bg-gradient-to-r from-orange-600 to-orange-400"
                        )}></div>
                        <CardContent className="relative pt-0 -mt-8 pb-4">
                            <div className="flex items-end gap-4 mb-4">
                                <div className={cn(
                                    "w-16 h-16 rounded-xl flex items-center justify-center text-2xl font-bold shadow-lg border-4",
                                    isDark ? "bg-gray-800 border-gray-900" : "bg-white border-white",
                                    getClassColor()
                                )}>
                                    {student.gender === 'L' ? '👦' : '👧'}
                                </div>
                                <div className="flex-1 pb-1">
                                    <span className={cn(
                                        "inline-block px-2 py-0.5 text-xs font-medium rounded border mb-1",
                                        getClassBadgeStyle()
                                    )}>
                                        {student.class_label}
                                    </span>
                                    <h2 className={cn("text-xl font-bold", isDark ? "text-white" : "text-gray-900")}>
                                        {student.name}
                                        {student.nickname && (
                                            <span className={cn("text-sm font-normal ml-2", isDark ? "text-gray-400" : "text-gray-500")}>
                                                ({student.nickname})
                                            </span>
                                        )}
                                    </h2>
                                    <p className={cn("text-sm font-mono", isDark ? "text-gray-400" : "text-gray-500")}>
                                        {student.nis}
                                    </p>
                                </div>
                            </div>

                            {/* Status Badge */}
                            <div className="flex items-center gap-2">
                                <span className={cn(
                                    "inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium",
                                    student.is_active
                                        ? isDark ? "bg-green-900/30 text-green-400" : "bg-green-100 text-green-700"
                                        : isDark ? "bg-red-900/30 text-red-400" : "bg-red-100 text-red-700"
                                )}>
                                    <span className={cn(
                                        "w-1.5 h-1.5 rounded-full",
                                        student.is_active ? "bg-green-500" : "bg-red-500"
                                    )}></span>
                                    {student.is_active ? 'Aktif' : 'Tidak Aktif'}
                                </span>
                                {student.age && (
                                    <span className={cn(
                                        "inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs",
                                        isDark ? "bg-indigo-900/30 text-indigo-400" : "bg-indigo-100 text-indigo-700"
                                    )}>
                                        <Calendar className="h-3 w-3" />
                                        {student.age.formatted}
                                    </span>
                                )}
                            </div>
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
                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <p className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>Tempat Lahir</p>
                                    <p className={cn("font-medium", isDark ? "text-white" : "text-gray-900")}>
                                        {student.birth_place || '-'}
                                    </p>
                                </div>
                                <div>
                                    <p className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>Tanggal Lahir</p>
                                    <p className={cn("font-medium", isDark ? "text-white" : "text-gray-900")}>
                                        {formatDate(student.birth_date)}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>Jenis Kelamin</p>
                                <p className={cn("font-medium", isDark ? "text-white" : "text-gray-900")}>
                                    {student.gender === 'L' ? 'Laki-laki' : 'Perempuan'}
                                </p>
                            </div>
                            <div className="flex items-start gap-2">
                                <MapPin className={cn("h-4 w-4 mt-0.5", isDark ? "text-gray-500" : "text-gray-400")} />
                                <div>
                                    <p className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>Alamat</p>
                                    <p className={cn("font-medium", isDark ? "text-white" : "text-gray-900")}>
                                        {student.address || '-'}
                                    </p>
                                </div>
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
                                <div className="flex items-center justify-between mb-2">
                                    <h4 className={cn("font-medium", isDark ? "text-blue-400" : "text-blue-600")}>👨 Ayah</h4>
                                    {student.father_whatsapp_link && (
                                        <a
                                            href={student.father_whatsapp_link}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className={cn(
                                                "inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium",
                                                isDark ? "bg-green-900/30 text-green-400" : "bg-green-100 text-green-700"
                                            )}
                                        >
                                            <MessageCircle className="h-3 w-3" />
                                            WA
                                        </a>
                                    )}
                                </div>
                                <div className="space-y-2">
                                    <div className="grid grid-cols-2 gap-2">
                                        <div>
                                            <p className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>Nama</p>
                                            <p className={cn("font-medium text-sm", isDark ? "text-white" : "text-gray-900")}>
                                                {student.father_name || '-'}
                                            </p>
                                        </div>
                                        <div>
                                            <p className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>Pekerjaan</p>
                                            <p className={cn("font-medium text-sm", isDark ? "text-white" : "text-gray-900")}>
                                                {student.father_occupation || '-'}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Phone className={cn("h-3 w-3", isDark ? "text-gray-500" : "text-gray-400")} />
                                        <span className={cn("text-sm", isDark ? "text-gray-300" : "text-gray-700")}>
                                            {student.father_phone || '-'}
                                        </span>
                                        {student.father_wa && student.father_wa !== student.father_phone && (
                                            <span className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>
                                                (WA: {student.father_wa})
                                            </span>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Data Ibu */}
                            <div className={cn("p-3 rounded-lg border", isDark ? "border-gray-700 bg-gray-800/30" : "border-gray-200 bg-gray-50")}>
                                <div className="flex items-center justify-between mb-2">
                                    <h4 className={cn("font-medium", isDark ? "text-pink-400" : "text-pink-600")}>👩 Ibu</h4>
                                    {student.mother_whatsapp_link && (
                                        <a
                                            href={student.mother_whatsapp_link}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className={cn(
                                                "inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium",
                                                isDark ? "bg-green-900/30 text-green-400" : "bg-green-100 text-green-700"
                                            )}
                                        >
                                            <MessageCircle className="h-3 w-3" />
                                            WA
                                        </a>
                                    )}
                                </div>
                                <div className="space-y-2">
                                    <div className="grid grid-cols-2 gap-2">
                                        <div>
                                            <p className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>Nama</p>
                                            <p className={cn("font-medium text-sm", isDark ? "text-white" : "text-gray-900")}>
                                                {student.mother_name || '-'}
                                            </p>
                                        </div>
                                        <div>
                                            <p className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>Pekerjaan</p>
                                            <p className={cn("font-medium text-sm", isDark ? "text-white" : "text-gray-900")}>
                                                {student.mother_occupation || '-'}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Phone className={cn("h-3 w-3", isDark ? "text-gray-500" : "text-gray-400")} />
                                        <span className={cn("text-sm", isDark ? "text-gray-300" : "text-gray-700")}>
                                            {student.mother_phone || '-'}
                                        </span>
                                        {student.mother_wa && student.mother_wa !== student.mother_phone && (
                                            <span className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>
                                                (WA: {student.mother_wa})
                                            </span>
                                        )}
                                    </div>
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
                                    <p className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>Tanggal Daftar</p>
                                    <p className={cn("font-medium", isDark ? "text-white" : "text-gray-900")}>
                                        {formatDate(student.registration_date)}
                                    </p>
                                </div>
                                <div>
                                    <p className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>Tahun Masuk</p>
                                    <p className={cn("font-medium", isDark ? "text-white" : "text-gray-900")}>
                                        {student.entry_year}
                                    </p>
                                </div>
                            </div>
                            <div className={cn(
                                "p-3 rounded-lg flex items-center justify-between",
                                isDark ? "bg-green-900/20" : "bg-green-50"
                            )}>
                                <div>
                                    <p className={cn("text-xs", isDark ? "text-green-400" : "text-green-600")}>Iuran Bulanan (SPP)</p>
                                    <p className={cn("text-xl font-bold font-mono", isDark ? "text-green-400" : "text-green-600")}>
                                        {student.formatted_monthly_fee}
                                    </p>
                                </div>
                                <CreditCard className={cn("h-8 w-8", isDark ? "text-green-600" : "text-green-300")} />
                            </div>
                        </CardContent>
                    </Card>
                </main>

                {/* Fixed Bottom Actions */}
                <div className="fixed bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-white dark:from-gray-900 via-white/90 dark:via-gray-900/90 to-transparent">
                    <div className="max-w-2xl mx-auto flex gap-3">
                        <Button
                            variant="outline"
                            onClick={() => router.visit(route('students.edit', student.id))}
                            className={cn(
                                "flex-1",
                                isDark ? "border-gray-700 text-gray-300" : "border-gray-200"
                            )}
                        >
                            <Edit2 className="h-4 w-4 mr-2" />
                            Edit
                        </Button>
                        <Button
                            className="flex-1 bg-green-600 hover:bg-green-700 text-white"
                        >
                            <CreditCard className="h-4 w-4 mr-2" />
                            Bayar SPP
                        </Button>
                    </div>
                </div>
            </div>
        </>
    );
}
