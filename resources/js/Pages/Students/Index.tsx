import { Head, router, Link } from '@inertiajs/react';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { ThemeToggle } from '@/Components/theme-toggle';
import { useIsDark } from '@/hooks/use-is-dark';
import { ArrowLeft, Plus, Search, Users, User, Filter, ChevronRight, Sun, Moon } from 'lucide-react';
import { cn } from '@/lib/utils';
import { useState } from 'react';

interface Student {
    id: number;
    nis: string;
    name: string;
    type: string;
    type_label: string;
    class_time: string;
    class_label: string;
    gender: string;
    parent_name: string;
    parent_phone: string;
    entry_year: number;
    monthly_fee: number;
    formatted_monthly_fee: string;
    is_active: boolean;
}

interface Props {
    students: {
        data: Student[];
        current_page: number;
        last_page: number;
        total: number;
    };
    entryYears: number[];
    filters: {
        type: string;
        class_time: string;
        status: string;
        entry_year: number | null;
        search: string | null;
    };
    stats: {
        total: number;
        active: number;
        tpq_pagi: number;
        tpq_sore: number;
        taud: number;
    };
}

export default function StudentsIndex({ students, entryYears, filters, stats }: Props) {
    const isDark = useIsDark();
    const [search, setSearch] = useState(filters.search || '');
    const [showFilters, setShowFilters] = useState(false);

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get(route('students.index'), { ...filters, search }, { preserveState: true });
    };

    const handleFilterChange = (key: string, value: string) => {
        router.get(route('students.index'), { ...filters, [key]: value }, { preserveState: true });
    };

    return (
        <>
            <Head title="Data Santri" />

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
            <div className="relative min-h-screen pb-20">
                {/* Header */}
                <header className={cn(
                    "sticky top-0 z-30 backdrop-blur-xl border-b",
                    isDark ? "bg-gray-900/80 border-gray-800/50" : "bg-white/80 border-gray-200/50"
                )}>
                    <div className="max-w-4xl mx-auto px-4">
                        <div className="flex h-14 items-center justify-between">
                            <div className="flex items-center gap-3">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    onClick={() => router.visit(route('dashboard'))}
                                    className={cn("h-9 w-9", isDark ? "text-gray-300" : "text-gray-600")}
                                >
                                    <ArrowLeft className="h-5 w-5" />
                                </Button>
                                <div>
                                    <h1 className={cn("text-lg font-bold", isDark ? "text-white" : "text-gray-900")}>
                                        Data Santri
                                    </h1>
                                    <p className={cn("text-xs", isDark ? "text-gray-400" : "text-gray-500")}>
                                        {stats.active} santri aktif
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <ThemeToggle />
                                <Link href={route('students.create')}>
                                    <Button size="sm" className="bg-indigo-600 hover:bg-indigo-700 text-white h-9">
                                        <Plus className="h-4 w-4" />
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </header>

                <main className="max-w-4xl mx-auto py-4 px-4">
                    {/* Stats Row - Scrollable */}
                    <div className="flex gap-2 overflow-x-auto pb-3 -mx-4 px-4 scrollbar-hide">
                        <div className={cn(
                            "flex-shrink-0 px-4 py-2 rounded-full border",
                            isDark ? "bg-gray-800 border-gray-700" : "bg-white border-gray-200"
                        )}>
                            <span className={cn("text-sm font-medium", isDark ? "text-white" : "text-gray-900")}>
                                <Users className="inline h-4 w-4 mr-1" />
                                {stats.active}
                            </span>
                        </div>
                        <div className={cn(
                            "flex-shrink-0 px-4 py-2 rounded-full border",
                            isDark ? "bg-green-900/30 border-green-800" : "bg-green-50 border-green-200"
                        )}>
                            <span className={cn("text-sm font-medium", isDark ? "text-green-400" : "text-green-700")}>
                                <Sun className="inline h-4 w-4 mr-1" />
                                TPQA: {stats.tpq_pagi}
                            </span>
                        </div>
                        <div className={cn(
                            "flex-shrink-0 px-4 py-2 rounded-full border",
                            isDark ? "bg-blue-900/30 border-blue-800" : "bg-blue-50 border-blue-200"
                        )}>
                            <span className={cn("text-sm font-medium", isDark ? "text-blue-400" : "text-blue-700")}>
                                <Moon className="inline h-4 w-4 mr-1" />
                                TPQB: {stats.tpq_sore}
                            </span>
                        </div>
                        <div className={cn(
                            "flex-shrink-0 px-4 py-2 rounded-full border",
                            isDark ? "bg-purple-900/30 border-purple-800" : "bg-purple-50 border-purple-200"
                        )}>
                            <span className={cn("text-sm font-medium", isDark ? "text-purple-400" : "text-purple-700")}>
                                <User className="inline h-4 w-4 mr-1" />
                                TAUD: {stats.taud}
                            </span>
                        </div>
                    </div>

                    {/* Search & Filters */}
                    <Card className={cn(
                        "mb-4 border backdrop-blur-sm",
                        isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                    )}>
                        <CardContent className="p-3">
                            <form onSubmit={handleSearch} className="flex gap-2">
                                <div className="relative flex-1">
                                    <Search className={cn(
                                        "absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4",
                                        isDark ? "text-gray-500" : "text-gray-400"
                                    )} />
                                    <Input
                                        placeholder="Cari nama atau NIS..."
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        className={cn(
                                            "pl-9 h-10",
                                            isDark ? "bg-gray-800 border-gray-700 text-white" : ""
                                        )}
                                    />
                                </div>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    onClick={() => setShowFilters(!showFilters)}
                                    className={cn(
                                        "h-10 w-10",
                                        showFilters ? "bg-indigo-600 text-white border-indigo-600" : isDark ? "border-gray-700" : ""
                                    )}
                                >
                                    <Filter className="h-4 w-4" />
                                </Button>
                            </form>

                            {showFilters && (
                                <div className="flex flex-wrap gap-2 pt-3 mt-3 border-t border-gray-200 dark:border-gray-700">
                                    <select
                                        value={filters.type}
                                        onChange={(e) => handleFilterChange('type', e.target.value)}
                                        className={cn(
                                            "px-3 py-1.5 rounded-md border text-sm",
                                            isDark ? "bg-gray-800 border-gray-700 text-white" : "bg-white border-gray-200"
                                        )}
                                    >
                                        <option value="all">Semua Program</option>
                                        <option value="tpq">TPQ</option>
                                        <option value="taud">TAUD</option>
                                    </select>

                                    <select
                                        value={filters.class_time}
                                        onChange={(e) => handleFilterChange('class_time', e.target.value)}
                                        className={cn(
                                            "px-3 py-1.5 rounded-md border text-sm",
                                            isDark ? "bg-gray-800 border-gray-700 text-white" : "bg-white border-gray-200"
                                        )}
                                    >
                                        <option value="all">Semua Kelas</option>
                                        <option value="pagi">Pagi</option>
                                        <option value="sore">Sore</option>
                                    </select>

                                    <select
                                        value={filters.status}
                                        onChange={(e) => handleFilterChange('status', e.target.value)}
                                        className={cn(
                                            "px-3 py-1.5 rounded-md border text-sm",
                                            isDark ? "bg-gray-800 border-gray-700 text-white" : "bg-white border-gray-200"
                                        )}
                                    >
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Tidak Aktif</option>
                                    </select>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Student List */}
                    <div className="space-y-2">
                        {students.data.length === 0 ? (
                            <Card className={cn(
                                "border backdrop-blur-sm",
                                isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                            )}>
                                <CardContent className="p-8 text-center">
                                    <Users className={cn("h-10 w-10 mx-auto mb-2", isDark ? "text-gray-600" : "text-gray-300")} />
                                    <p className={isDark ? "text-gray-400" : "text-gray-500"}>
                                        Tidak ada data santri
                                    </p>
                                </CardContent>
                            </Card>
                        ) : (
                            students.data.map((student) => (
                                <Link key={student.id} href={route('students.show', student.id)}>
                                    <Card className={cn(
                                        "border backdrop-blur-sm cursor-pointer transition-all active:scale-[0.98]",
                                        isDark
                                            ? "bg-gray-900/50 border-gray-800 hover:border-gray-700"
                                            : "bg-white border-gray-200 hover:border-gray-300"
                                    )}>
                                        <CardContent className="p-3">
                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center gap-3 min-w-0">
                                                    <div className={cn(
                                                        "w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0",
                                                        student.type === 'taud'
                                                            ? (isDark ? "bg-purple-900/30 text-purple-400" : "bg-purple-100 text-purple-600")
                                                            : student.class_time === 'pagi'
                                                                ? (isDark ? "bg-green-900/30 text-green-400" : "bg-green-100 text-green-600")
                                                                : (isDark ? "bg-blue-900/30 text-blue-400" : "bg-blue-100 text-blue-600")
                                                    )}>
                                                        {student.name.charAt(0).toUpperCase()}
                                                    </div>
                                                    <div className="min-w-0">
                                                        <p className={cn("font-medium truncate", isDark ? "text-white" : "text-gray-900")}>
                                                            {student.name}
                                                        </p>
                                                        <div className="flex items-center gap-2">
                                                            <span className={cn(
                                                                "text-xs px-1.5 py-0.5 rounded font-medium",
                                                                student.type === 'taud'
                                                                    ? (isDark ? "bg-purple-900/30 text-purple-400" : "bg-purple-100 text-purple-600")
                                                                    : student.class_time === 'pagi'
                                                                        ? (isDark ? "bg-green-900/30 text-green-400" : "bg-green-100 text-green-600")
                                                                        : (isDark ? "bg-blue-900/30 text-blue-400" : "bg-blue-100 text-blue-600")
                                                            )}>
                                                                {student.type === 'taud' ? 'TAUD' : (student.class_time === 'pagi' ? 'TPQA' : 'TPQB')}
                                                            </span>
                                                            <span className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>
                                                                {student.nis}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="flex items-center gap-2">
                                                    <span className={cn("text-sm font-mono", isDark ? "text-indigo-400" : "text-indigo-600")}>
                                                        {student.formatted_monthly_fee}
                                                    </span>
                                                    <ChevronRight className={cn("h-4 w-4", isDark ? "text-gray-600" : "text-gray-400")} />
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </Link>
                            ))
                        )}
                    </div>

                    {/* Pagination */}
                    {students.last_page > 1 && (
                        <div className="flex justify-center gap-1 mt-4">
                            {Array.from({ length: students.last_page }, (_, i) => i + 1).map((page) => (
                                <Button
                                    key={page}
                                    variant={page === students.current_page ? "default" : "outline"}
                                    size="sm"
                                    onClick={() => router.get(route('students.index'), { ...filters, page })}
                                    className={cn(
                                        "h-8 w-8 p-0",
                                        page === students.current_page
                                            ? "bg-indigo-600 text-white"
                                            : (isDark ? "border-gray-700" : "")
                                    )}
                                >
                                    {page}
                                </Button>
                            ))}
                        </div>
                    )}
                </main>

                {/* FAB for adding student */}
                <Link href={route('students.create')} className="fixed bottom-6 right-6 md:hidden">
                    <Button className="h-14 w-14 rounded-full bg-indigo-600 hover:bg-indigo-700 shadow-lg">
                        <Plus className="h-6 w-6" />
                    </Button>
                </Link>
            </div>
        </>
    );
}
