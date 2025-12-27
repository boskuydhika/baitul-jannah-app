import { Head, router, Link } from '@inertiajs/react';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { ThemeToggle } from '@/Components/theme-toggle';
import { useIsDark } from '@/hooks/use-is-dark';
import { ArrowLeft, Plus, Search, Filter, TrendingUp, TrendingDown, AlertCircle, ChevronLeft, ChevronRight, ChevronDown, ChevronUp } from 'lucide-react';
import { cn } from '@/lib/utils';
import { useState } from 'react';

interface Category {
    id: number;
    name: string;
    type: string;
    type_label: string;
}

interface Transaction {
    id: number;
    transaction_number: string;
    transaction_datetime: string;
    formatted_datetime: string;
    type: string;
    type_label: string;
    category?: Category;
    description: string;
    amount: number;
    formatted_amount: string;
    status: string;
    status_label: string;
}

interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    transactions: {
        data: Transaction[];
    } & Pagination;
    currentBalance: number;
    draftCount: number;
    categories: Category[];
    filters: {
        type: string;
        category_id: number | null;
        status: string;
        start_date: string | null;
        end_date: string | null;
        search: string | null;
    };
}

// Format currency
function formatCurrency(value: number): string {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
}

// Collapsible text component
function CollapsibleText({ text, maxLength = 80, isDark }: { text: string; maxLength?: number; isDark: boolean }) {
    const [expanded, setExpanded] = useState(false);

    if (text.length <= maxLength) {
        return <span>{text}</span>;
    }

    return (
        <span>
            {expanded ? text : text.substring(0, maxLength) + '...'}
            <button
                onClick={(e) => { e.stopPropagation(); setExpanded(!expanded); }}
                className={cn("ml-1 text-xs font-medium", isDark ? "text-indigo-400" : "text-indigo-600")}
            >
                {expanded ? 'Tutup' : 'Selengkapnya'}
            </button>
        </span>
    );
}

export default function TransactionIndex({ transactions, currentBalance, draftCount, categories, filters }: Props) {
    const isDark = useIsDark();
    const [showFilters, setShowFilters] = useState(false);
    const [localFilters, setLocalFilters] = useState(filters);

    const handleFilterChange = (key: string, value: any) => {
        setLocalFilters(prev => ({ ...prev, [key]: value }));
    };

    const applyFilters = () => {
        router.get(route('finance.transactions.index'), localFilters, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const resetFilters = () => {
        router.get(route('finance.transactions.index'));
    };

    return (
        <>
            <Head title="Buku Kas" />

            {/* Background Layers */}
            <div className="fixed inset-0">
                <div className={cn(
                    "absolute inset-0 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100",
                    isDark ? "opacity-0 pointer-events-none" : "opacity-100"
                )}></div>
                <div className={cn(
                    "absolute inset-0",
                    isDark ? "opacity-100" : "opacity-0 pointer-events-none"
                )}>
                    <div className="absolute inset-0 bg-gradient-to-br from-gray-950 via-slate-900 to-gray-900"></div>
                    <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-900/20 rounded-full blur-3xl"></div>
                    <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-900/10 rounded-full blur-3xl"></div>
                </div>
            </div>

            {/* Content */}
            <div className="relative min-h-screen">
                {/* Header */}
                <header className={cn(
                    "sticky top-0 z-30 backdrop-blur-xl border-b",
                    isDark ? "bg-gray-900/80 border-gray-800/50" : "bg-white/80 border-gray-200/50"
                )}>
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex h-16 items-center justify-between">
                            <div className="flex items-center gap-4">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    onClick={() => router.visit(route('dashboard'))}
                                    className={cn("h-10 w-10", isDark ? "text-gray-300" : "text-gray-600")}
                                >
                                    <ArrowLeft className="h-5 w-5" />
                                </Button>
                                <div>
                                    <h1 className={cn("text-lg md:text-xl font-bold", isDark ? "text-white" : "text-gray-900")}>
                                        Buku Kas
                                    </h1>
                                    <p className={cn("text-xs hidden sm:block", isDark ? "text-gray-400" : "text-gray-500")}>
                                        Pencatatan pemasukan & pengeluaran
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <ThemeToggle />
                                <Link href={route('finance.transactions.create')}>
                                    <Button className="hidden sm:flex bg-indigo-600 hover:bg-indigo-700 text-white">
                                        <Plus className="h-4 w-4 mr-2" />
                                        Catat Transaksi
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </header>

                <main className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {/* Balance Card - Sticky on top */}
                    <Card className={cn(
                        "mb-6 border backdrop-blur-sm overflow-hidden",
                        isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                    )}>
                        <CardContent className="p-6">
                            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <p className={cn("text-sm font-medium mb-1", isDark ? "text-gray-400" : "text-gray-500")}>
                                        💰 Saldo Saat Ini
                                    </p>
                                    <p className={cn(
                                        "text-3xl md:text-4xl font-bold font-mono",
                                        currentBalance >= 0
                                            ? (isDark ? "text-green-400" : "text-green-600")
                                            : (isDark ? "text-red-400" : "text-red-600")
                                    )}>
                                        {formatCurrency(currentBalance)}
                                    </p>
                                </div>

                                {/* Draft Alert */}
                                {draftCount > 0 && (
                                    <div className={cn(
                                        "flex items-center gap-2 px-4 py-2 rounded-lg",
                                        isDark ? "bg-yellow-900/30 text-yellow-400" : "bg-yellow-50 text-yellow-700"
                                    )}>
                                        <AlertCircle className="h-4 w-4" />
                                        <span className="text-sm font-medium">
                                            {draftCount} transaksi draft menunggu
                                        </span>
                                    </div>
                                )}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Mobile: Add Button */}
                    <div className="sm:hidden mb-4">
                        <Link href={route('finance.transactions.create')} className="block">
                            <Button className="w-full bg-indigo-600 hover:bg-indigo-700 text-white">
                                <Plus className="h-4 w-4 mr-2" />
                                Catat Transaksi
                            </Button>
                        </Link>
                    </div>

                    {/* Filters */}
                    <Card className={cn(
                        "mb-6 border backdrop-blur-sm",
                        isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                    )}>
                        <CardContent className="p-4">
                            <div className="flex flex-col gap-4">
                                <div className="flex gap-3">
                                    {/* Search */}
                                    <div className="flex-1 relative">
                                        <Search className={cn(
                                            "absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4",
                                            isDark ? "text-gray-500" : "text-gray-400"
                                        )} />
                                        <Input
                                            placeholder="Cari transaksi..."
                                            value={localFilters.search || ''}
                                            onChange={(e) => handleFilterChange('search', e.target.value)}
                                            onKeyDown={(e) => e.key === 'Enter' && applyFilters()}
                                            className={cn(
                                                "pl-9",
                                                isDark ? "bg-gray-800 border-gray-700 text-white placeholder:text-gray-500" : ""
                                            )}
                                        />
                                    </div>

                                    {/* Filter Toggle */}
                                    <Button
                                        variant="outline"
                                        onClick={() => setShowFilters(!showFilters)}
                                        className={isDark ? "border-gray-700 text-gray-300" : ""}
                                    >
                                        <Filter className="h-4 w-4 mr-2" />
                                        Filter
                                        {showFilters ? <ChevronUp className="h-4 w-4 ml-1" /> : <ChevronDown className="h-4 w-4 ml-1" />}
                                    </Button>
                                </div>

                                {/* Collapsible Filters */}
                                {showFilters && (
                                    <div className="flex flex-wrap gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                        {/* Type Filter */}
                                        <select
                                            value={localFilters.type}
                                            onChange={(e) => handleFilterChange('type', e.target.value)}
                                            className={cn(
                                                "h-10 px-3 rounded-md border text-sm",
                                                isDark ? "bg-gray-800 border-gray-700 text-white" : "bg-white border-gray-200"
                                            )}
                                        >
                                            <option value="all">Semua Tipe</option>
                                            <option value="income">Pemasukan</option>
                                            <option value="expense">Pengeluaran</option>
                                        </select>

                                        {/* Status Filter */}
                                        <select
                                            value={localFilters.status}
                                            onChange={(e) => handleFilterChange('status', e.target.value)}
                                            className={cn(
                                                "h-10 px-3 rounded-md border text-sm",
                                                isDark ? "bg-gray-800 border-gray-700 text-white" : "bg-white border-gray-200"
                                            )}
                                        >
                                            <option value="all">Semua Status</option>
                                            <option value="draft">Draft</option>
                                            <option value="posted">Posted</option>
                                        </select>

                                        <Button onClick={applyFilters} className="bg-indigo-600 hover:bg-indigo-700 text-white">
                                            Terapkan
                                        </Button>
                                        <Button variant="outline" onClick={resetFilters} className={isDark ? "border-gray-700 text-gray-300" : ""}>
                                            Reset
                                        </Button>
                                    </div>
                                )}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Transactions Table */}
                    <Card className={cn(
                        "border backdrop-blur-sm overflow-hidden",
                        isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                    )}>
                        {/* Desktop Table */}
                        <div className="hidden md:block overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className={cn(
                                        "border-b text-left text-sm",
                                        isDark ? "border-gray-800 bg-gray-800/50 text-gray-300" : "border-gray-200 bg-gray-50 text-gray-600"
                                    )}>
                                        <th className="px-4 py-3 font-semibold">Tanggal & Waktu</th>
                                        <th className="px-4 py-3 font-semibold">Kategori</th>
                                        <th className="px-4 py-3 font-semibold">Uraian</th>
                                        <th className="px-4 py-3 font-semibold text-right">Masuk</th>
                                        <th className="px-4 py-3 font-semibold text-right">Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {transactions.data.map((trx, idx) => (
                                        <tr
                                            key={trx.id}
                                            onClick={() => router.visit(route('finance.transactions.show', trx.id))}
                                            className={cn(
                                                "border-b transition-colors cursor-pointer",
                                                isDark ? "border-gray-800 hover:bg-indigo-900/20" : "border-gray-100 hover:bg-indigo-50/50",
                                                trx.status === 'draft' && (isDark ? "bg-yellow-900/10" : "bg-yellow-50/50")
                                            )}
                                        >
                                            <td className={cn("px-4 py-3", isDark ? "text-gray-300" : "text-gray-600")}>
                                                <div className="flex items-center gap-2">
                                                    {trx.status === 'draft' && (
                                                        <span className={cn(
                                                            "text-xs px-1.5 py-0.5 rounded",
                                                            isDark ? "bg-yellow-900/30 text-yellow-400" : "bg-yellow-100 text-yellow-700"
                                                        )}>Draft</span>
                                                    )}
                                                    <span className="text-sm">{trx.formatted_datetime}</span>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className={cn(
                                                    "inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-sm font-medium",
                                                    trx.type === 'income'
                                                        ? (isDark ? "bg-green-900/30 text-green-400" : "bg-green-50 text-green-700")
                                                        : (isDark ? "bg-red-900/30 text-red-400" : "bg-red-50 text-red-700")
                                                )}>
                                                    {trx.type === 'income' ? <TrendingUp className="h-3.5 w-3.5" /> : <TrendingDown className="h-3.5 w-3.5" />}
                                                    {trx.category?.name || trx.type_label}
                                                </span>
                                            </td>
                                            <td className={cn("px-4 py-3 max-w-xs", isDark ? "text-white" : "text-gray-900")}>
                                                <CollapsibleText text={trx.description} isDark={isDark} />
                                            </td>
                                            <td className={cn("px-4 py-3 text-right font-mono",
                                                trx.type === 'income' ? (isDark ? "text-green-400 font-semibold" : "text-green-600 font-semibold") : (isDark ? "text-gray-500" : "text-gray-400")
                                            )}>
                                                {trx.type === 'income' ? trx.formatted_amount : '-'}
                                            </td>
                                            <td className={cn("px-4 py-3 text-right font-mono",
                                                trx.type === 'expense' ? (isDark ? "text-red-400 font-semibold" : "text-red-600 font-semibold") : (isDark ? "text-gray-500" : "text-gray-400")
                                            )}>
                                                {trx.type === 'expense' ? trx.formatted_amount : '-'}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Mobile Cards */}
                        <div className="md:hidden divide-y divide-gray-200 dark:divide-gray-800">
                            {transactions.data.map((trx) => (
                                <div
                                    key={trx.id}
                                    onClick={() => router.visit(route('finance.transactions.show', trx.id))}
                                    className={cn(
                                        "p-4 cursor-pointer transition-colors",
                                        isDark ? "hover:bg-gray-800/50" : "hover:bg-gray-50",
                                        trx.status === 'draft' && (isDark ? "bg-yellow-900/10" : "bg-yellow-50/50")
                                    )}
                                >
                                    <div className="flex items-start justify-between mb-2">
                                        <div className="flex items-center gap-2">
                                            <div className={cn(
                                                "w-8 h-8 rounded-lg flex items-center justify-center",
                                                trx.type === 'income'
                                                    ? (isDark ? "bg-green-900/30" : "bg-green-100")
                                                    : (isDark ? "bg-red-900/30" : "bg-red-100")
                                            )}>
                                                {trx.type === 'income'
                                                    ? <TrendingUp className={cn("h-4 w-4", isDark ? "text-green-400" : "text-green-600")} />
                                                    : <TrendingDown className={cn("h-4 w-4", isDark ? "text-red-400" : "text-red-600")} />
                                                }
                                            </div>
                                            <div>
                                                <p className={cn("text-sm font-medium", isDark ? "text-white" : "text-gray-900")}>
                                                    {trx.category?.name || trx.type_label}
                                                </p>
                                                <p className={cn("text-xs", isDark ? "text-gray-500" : "text-gray-400")}>
                                                    {trx.formatted_datetime}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            {trx.status === 'draft' && (
                                                <span className={cn(
                                                    "text-xs px-1.5 py-0.5 rounded mb-1 inline-block",
                                                    isDark ? "bg-yellow-900/30 text-yellow-400" : "bg-yellow-100 text-yellow-700"
                                                )}>Draft</span>
                                            )}
                                            <p className={cn(
                                                "text-lg font-bold font-mono",
                                                trx.type === 'income'
                                                    ? (isDark ? "text-green-400" : "text-green-600")
                                                    : (isDark ? "text-red-400" : "text-red-600")
                                            )}>
                                                {trx.type === 'expense' && '-'}{trx.formatted_amount}
                                            </p>
                                        </div>
                                    </div>
                                    <p className={cn("text-sm line-clamp-2", isDark ? "text-gray-400" : "text-gray-600")}>
                                        {trx.description}
                                    </p>
                                </div>
                            ))}
                        </div>

                        {/* Pagination */}
                        {transactions.last_page > 1 && (
                            <div className={cn(
                                "flex items-center justify-between px-4 py-3 border-t",
                                isDark ? "border-gray-800" : "border-gray-200"
                            )}>
                                <p className={cn("text-sm", isDark ? "text-gray-400" : "text-gray-500")}>
                                    {transactions.data.length} dari {transactions.total}
                                </p>
                                <div className="flex gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        disabled={transactions.current_page === 1}
                                        onClick={() => router.get(route('finance.transactions.index', { ...filters, page: transactions.current_page - 1 }))}
                                        className={isDark ? "border-gray-700 text-gray-300" : ""}
                                    >
                                        <ChevronLeft className="h-4 w-4" />
                                    </Button>
                                    <span className={cn("flex items-center px-3 text-sm", isDark ? "text-gray-300" : "text-gray-600")}>
                                        {transactions.current_page} / {transactions.last_page}
                                    </span>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        disabled={transactions.current_page === transactions.last_page}
                                        onClick={() => router.get(route('finance.transactions.index', { ...filters, page: transactions.current_page + 1 }))}
                                        className={isDark ? "border-gray-700 text-gray-300" : ""}
                                    >
                                        <ChevronRight className="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        )}
                    </Card>

                    {/* Empty State */}
                    {transactions.data.length === 0 && (
                        <Card className={cn(
                            "p-12 text-center border backdrop-blur-sm",
                            isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                        )}>
                            <div className={cn(
                                "w-16 h-16 mx-auto rounded-2xl flex items-center justify-center mb-4",
                                isDark ? "bg-gray-800" : "bg-gray-100"
                            )}>
                                <span className="text-3xl">📒</span>
                            </div>
                            <h3 className={cn("text-lg font-semibold mb-2", isDark ? "text-white" : "text-gray-900")}>
                                Belum ada transaksi
                            </h3>
                            <p className={cn("mb-6 max-w-sm mx-auto", isDark ? "text-gray-400" : "text-gray-500")}>
                                Mulai mencatat pemasukan dan pengeluaran Anda
                            </p>
                            <Link href={route('finance.transactions.create')}>
                                <Button className="bg-indigo-600 hover:bg-indigo-700 text-white">
                                    <Plus className="h-4 w-4 mr-2" />
                                    Catat Transaksi Pertama
                                </Button>
                            </Link>
                        </Card>
                    )}
                </main>
            </div>
        </>
    );
}
