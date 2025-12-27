import { Head, router, Link } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { ThemeToggle } from '@/Components/theme-toggle';
import { useIsDark } from '@/hooks/use-is-dark';
import { ArrowLeft, TrendingUp, TrendingDown, Calendar, Tag, User, FileText, Edit, Trash2, CheckCircle } from 'lucide-react';
import { cn } from '@/lib/utils';
import { useState } from 'react';

interface Category {
    id: number;
    name: string;
    type: string;
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
    creator?: {
        id: number;
        name: string;
    };
}

interface Props {
    transaction: Transaction;
    canEdit?: boolean;
}

export default function TransactionShow({ transaction, canEdit = false }: Props) {
    const isDark = useIsDark();
    const [isProcessing, setIsProcessing] = useState(false);

    const isIncome = transaction.type === 'income';
    const isDraft = transaction.status === 'draft';
    const showEditButton = isDraft || canEdit; // Show edit for draft or if canEdit is true (super admin)

    const handlePost = () => {
        if (confirm('Posting transaksi ini? Setelah diposting, transaksi tidak bisa diedit.')) {
            setIsProcessing(true);
            router.post(route('finance.transactions.post', transaction.id), {}, {
                onFinish: () => setIsProcessing(false),
            });
        }
    };

    const handleDelete = () => {
        if (confirm('Hapus transaksi draft ini?')) {
            setIsProcessing(true);
            router.delete(route('finance.transactions.destroy', transaction.id), {
                onFinish: () => setIsProcessing(false),
            });
        }
    };

    const handleUnpost = () => {
        if (confirm('Ubah transaksi ini menjadi Draft? Transaksi akan bisa diedit kembali.')) {
            setIsProcessing(true);
            router.post(route('finance.transactions.unpost', transaction.id), {}, {
                onFinish: () => setIsProcessing(false),
            });
        }
    };

    return (
        <>
            <Head title={`Transaksi ${transaction.transaction_number}`} />

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
                    <div className="max-w-2xl mx-auto px-4 sm:px-6">
                        <div className="flex h-16 items-center justify-between">
                            <div className="flex items-center gap-4">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    onClick={() => router.visit(route('finance.transactions.index'))}
                                    className={cn("h-10 w-10", isDark ? "text-gray-300" : "text-gray-600")}
                                >
                                    <ArrowLeft className="h-5 w-5" />
                                </Button>
                                <div>
                                    <h1 className={cn("text-lg md:text-xl font-bold font-mono", isDark ? "text-indigo-400" : "text-indigo-600")}>
                                        {transaction.transaction_number}
                                    </h1>
                                    <div className="flex items-center gap-2">
                                        <span className={cn(
                                            "text-xs px-2 py-0.5 rounded-full font-medium",
                                            isDraft
                                                ? (isDark ? "bg-yellow-900/30 text-yellow-400" : "bg-yellow-100 text-yellow-700")
                                                : (isDark ? "bg-green-900/30 text-green-400" : "bg-green-100 text-green-700")
                                        )}>
                                            {transaction.status_label}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <ThemeToggle />
                        </div>
                    </div>
                </header>

                <main className="max-w-2xl mx-auto py-6 px-4 sm:px-6">
                    {/* Amount Card */}
                    <Card className={cn(
                        "mb-6 border backdrop-blur-sm overflow-hidden",
                        isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                    )}>
                        <CardContent className="p-6">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-4">
                                    <div className={cn(
                                        "w-14 h-14 rounded-2xl flex items-center justify-center",
                                        isIncome
                                            ? (isDark ? "bg-green-900/30" : "bg-green-100")
                                            : (isDark ? "bg-red-900/30" : "bg-red-100")
                                    )}>
                                        {isIncome
                                            ? <TrendingUp className={cn("h-7 w-7", isDark ? "text-green-400" : "text-green-600")} />
                                            : <TrendingDown className={cn("h-7 w-7", isDark ? "text-red-400" : "text-red-600")} />
                                        }
                                    </div>
                                    <div>
                                        <p className={cn("text-sm font-medium", isDark ? "text-gray-400" : "text-gray-500")}>
                                            {transaction.type_label}
                                        </p>
                                        <p className={cn(
                                            "text-3xl font-bold font-mono",
                                            isIncome
                                                ? (isDark ? "text-green-400" : "text-green-600")
                                                : (isDark ? "text-red-400" : "text-red-600")
                                        )}>
                                            {!isIncome && '-'}{transaction.formatted_amount}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Details */}
                    <Card className={cn(
                        "mb-6 border backdrop-blur-sm",
                        isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                    )}>
                        <CardHeader className="pb-4">
                            <CardTitle className={cn("text-lg", isDark ? "text-white" : "text-gray-900")}>
                                Detail Transaksi
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {/* Date & Time */}
                            <div className="flex items-start gap-3">
                                <div className={cn(
                                    "w-10 h-10 rounded-xl flex items-center justify-center",
                                    isDark ? "bg-indigo-900/30" : "bg-indigo-100"
                                )}>
                                    <Calendar className={cn("h-5 w-5", isDark ? "text-indigo-400" : "text-indigo-600")} />
                                </div>
                                <div>
                                    <p className={cn("text-xs", isDark ? "text-gray-400" : "text-gray-500")}>
                                        Tanggal & Waktu
                                    </p>
                                    <p className={cn("font-medium", isDark ? "text-white" : "text-gray-900")}>
                                        {transaction.formatted_datetime}
                                    </p>
                                </div>
                            </div>

                            {/* Category */}
                            <div className="flex items-start gap-3">
                                <div className={cn(
                                    "w-10 h-10 rounded-xl flex items-center justify-center",
                                    isDark ? "bg-indigo-900/30" : "bg-indigo-100"
                                )}>
                                    <Tag className={cn("h-5 w-5", isDark ? "text-indigo-400" : "text-indigo-600")} />
                                </div>
                                <div>
                                    <p className={cn("text-xs", isDark ? "text-gray-400" : "text-gray-500")}>
                                        Kategori
                                    </p>
                                    <p className={cn("font-medium", isDark ? "text-white" : "text-gray-900")}>
                                        {transaction.category?.name || '-'}
                                    </p>
                                </div>
                            </div>

                            {/* Creator */}
                            <div className="flex items-start gap-3">
                                <div className={cn(
                                    "w-10 h-10 rounded-xl flex items-center justify-center",
                                    isDark ? "bg-indigo-900/30" : "bg-indigo-100"
                                )}>
                                    <User className={cn("h-5 w-5", isDark ? "text-indigo-400" : "text-indigo-600")} />
                                </div>
                                <div>
                                    <p className={cn("text-xs", isDark ? "text-gray-400" : "text-gray-500")}>
                                        Dicatat Oleh
                                    </p>
                                    <p className={cn("font-medium", isDark ? "text-white" : "text-gray-900")}>
                                        {transaction.creator?.name || '-'}
                                    </p>
                                </div>
                            </div>

                            {/* Description */}
                            <div className={cn(
                                "p-4 rounded-lg mt-4",
                                isDark ? "bg-gray-800/50" : "bg-gray-50"
                            )}>
                                <div className="flex items-center gap-2 mb-2">
                                    <FileText className={cn("h-4 w-4", isDark ? "text-gray-400" : "text-gray-500")} />
                                    <p className={cn("text-xs font-medium", isDark ? "text-gray-400" : "text-gray-500")}>
                                        Uraian
                                    </p>
                                </div>
                                <p className={cn("whitespace-pre-wrap", isDark ? "text-white" : "text-gray-900")}>
                                    {transaction.description}
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Actions for Draft */}
                    {isDraft && (
                        <div className="flex flex-col sm:flex-row gap-3">
                            <Link href={route('finance.transactions.edit', transaction.id)} className="flex-1">
                                <Button
                                    variant="outline"
                                    className={cn(
                                        "w-full",
                                        isDark ? "border-gray-700 text-gray-300" : ""
                                    )}
                                >
                                    <Edit className="h-4 w-4 mr-2" />
                                    Edit
                                </Button>
                            </Link>
                            <Button
                                onClick={handlePost}
                                disabled={isProcessing}
                                className="flex-1 bg-green-600 hover:bg-green-700 text-white"
                            >
                                <CheckCircle className="h-4 w-4 mr-2" />
                                {isProcessing ? 'Memproses...' : 'Posting'}
                            </Button>
                            <Button
                                variant="outline"
                                onClick={handleDelete}
                                disabled={isProcessing}
                                className={cn(
                                    "flex-1",
                                    isDark ? "border-red-800 text-red-400 hover:bg-red-900/20" : "border-red-200 text-red-600 hover:bg-red-50"
                                )}
                            >
                                <Trash2 className="h-4 w-4 mr-2" />
                                Hapus
                            </Button>
                        </div>
                    )}

                    {/* Actions for Super Admin on Posted transactions */}
                    {!isDraft && canEdit && (
                        <div className="flex flex-col sm:flex-row gap-3">
                            <Link href={route('finance.transactions.edit', transaction.id)} className="flex-1">
                                <Button
                                    variant="outline"
                                    className={cn(
                                        "w-full",
                                        isDark ? "border-indigo-800 text-indigo-400 hover:bg-indigo-900/20" : "border-indigo-200 text-indigo-600 hover:bg-indigo-50"
                                    )}
                                >
                                    <Edit className="h-4 w-4 mr-2" />
                                    Edit (Super Admin)
                                </Button>
                            </Link>
                            <Button
                                variant="outline"
                                onClick={handleUnpost}
                                disabled={isProcessing}
                                className={cn(
                                    "flex-1",
                                    isDark ? "border-yellow-800 text-yellow-400 hover:bg-yellow-900/20" : "border-yellow-200 text-yellow-600 hover:bg-yellow-50"
                                )}
                            >
                                <FileText className="h-4 w-4 mr-2" />
                                {isProcessing ? 'Memproses...' : 'Ubah ke Draft'}
                            </Button>
                        </div>
                    )}
                </main>
            </div>
        </>
    );
}
