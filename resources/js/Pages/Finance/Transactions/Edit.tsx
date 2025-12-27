import { Head, router } from '@inertiajs/react';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { ThemeToggle } from '@/Components/theme-toggle';
import { useIsDark } from '@/hooks/use-is-dark';
import { ArrowLeft, TrendingUp, TrendingDown, Search } from 'lucide-react';
import { cn } from '@/lib/utils';
import { useState, useMemo } from 'react';

interface Category {
    id: number;
    name: string;
    type: string;
}

interface Transaction {
    id: number;
    transaction_number: string;
    transaction_datetime: string;
    type: string;
    category_id: number | null;
    description: string;
    amount: number;
    status: string;
}

interface Props {
    transaction: Transaction;
    categories: Category[];
    canEdit?: boolean;
}

export default function TransactionEdit({ transaction, categories, canEdit = true }: Props) {
    const isDark = useIsDark();

    // Format datetime for input
    const formatDatetimeForInput = (datetime: string): string => {
        if (!datetime) return '';
        const dt = new Date(datetime);
        return dt.toISOString().slice(0, 16);
    };

    // Initialize amount - handle decimal values from database (75000.00 -> 75000)
    const getInitialAmount = () => {
        const amt = transaction.amount;
        // If it's a string like "75000.00", parse as float and floor
        if (typeof amt === 'string') {
            return Math.floor(parseFloat(amt)).toString();
        }
        // If it's a number, just floor it
        return Math.floor(amt).toString();
    };

    // Form state
    const [type, setType] = useState<'income' | 'expense'>(transaction.type as 'income' | 'expense');
    const [categoryId, setCategoryId] = useState<number | ''>(transaction.category_id || '');
    const [amount, setAmount] = useState(getInitialAmount());
    const [description, setDescription] = useState(transaction.description || '');
    const [transactionDatetime, setTransactionDatetime] = useState(formatDatetimeForInput(transaction.transaction_datetime));
    const [categorySearch, setCategorySearch] = useState('');
    const [showCategoryDropdown, setShowCategoryDropdown] = useState(false);
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Filter categories by type and search
    const filteredCategories = useMemo(() => {
        return categories
            .filter(cat => cat.type === type)
            .filter(cat => cat.name.toLowerCase().includes(categorySearch.toLowerCase()));
    }, [categories, type, categorySearch]);

    // Selected category
    const selectedCategory = categories.find(cat => cat.id === categoryId);

    // Format amount display (Indonesian format)
    const formatDisplayAmount = (value: string): string => {
        const num = value.replace(/\D/g, '');
        if (!num) return '';
        return new Intl.NumberFormat('id-ID').format(parseInt(num));
    };

    // Handle amount input
    const handleAmountChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const raw = e.target.value.replace(/\D/g, '');
        setAmount(raw);
    };

    // Reset category when type changes
    const handleTypeChange = (newType: 'income' | 'expense') => {
        setType(newType);
        setCategoryId('');
        setCategorySearch('');
    };

    // Select category
    const handleSelectCategory = (cat: Category) => {
        setCategoryId(cat.id);
        setCategorySearch('');
        setShowCategoryDropdown(false);
    };

    // Validate form
    const isValid = categoryId && amount && parseInt(amount) > 0 && description.trim() && transactionDatetime;

    // Submit form
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!isValid) return;

        setIsSubmitting(true);

        router.put(route('finance.transactions.update', transaction.id), {
            type,
            category_id: categoryId,
            amount: parseInt(amount),
            description: description.trim(),
            transaction_datetime: transactionDatetime,
        }, {
            onFinish: () => setIsSubmitting(false),
        });
    };

    return (
        <>
            <Head title="Edit Transaksi" />

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
                                    onClick={() => router.visit(route('finance.transactions.show', transaction.id))}
                                    className={cn("h-10 w-10", isDark ? "text-gray-300" : "text-gray-600")}
                                >
                                    <ArrowLeft className="h-5 w-5" />
                                </Button>
                                <div>
                                    <h1 className={cn("text-lg md:text-xl font-bold", isDark ? "text-white" : "text-gray-900")}>
                                        Edit Transaksi
                                    </h1>
                                    <p className={cn("text-xs font-mono", isDark ? "text-gray-400" : "text-gray-500")}>
                                        {transaction.transaction_number}
                                    </p>
                                </div>
                            </div>
                            <ThemeToggle />
                        </div>
                    </div>
                </header>

                <main className="max-w-2xl mx-auto py-6 px-4 sm:px-6">
                    <form onSubmit={handleSubmit}>
                        {/* Type Selection */}
                        <Card className={cn(
                            "mb-6 border backdrop-blur-sm overflow-hidden",
                            isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                        )}>
                            <CardContent className="p-0">
                                <div className="grid grid-cols-2">
                                    <button
                                        type="button"
                                        onClick={() => handleTypeChange('income')}
                                        className={cn(
                                            "flex flex-col items-center gap-2 py-6 transition-all",
                                            type === 'income'
                                                ? (isDark ? "bg-green-900/30 text-green-400" : "bg-green-50 text-green-700")
                                                : (isDark ? "text-gray-400 hover:bg-gray-800" : "text-gray-500 hover:bg-gray-50")
                                        )}
                                    >
                                        <TrendingUp className="h-8 w-8" />
                                        <span className="font-semibold">Pemasukan</span>
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => handleTypeChange('expense')}
                                        className={cn(
                                            "flex flex-col items-center gap-2 py-6 transition-all border-l",
                                            isDark ? "border-gray-800" : "border-gray-200",
                                            type === 'expense'
                                                ? (isDark ? "bg-red-900/30 text-red-400" : "bg-red-50 text-red-700")
                                                : (isDark ? "text-gray-400 hover:bg-gray-800" : "text-gray-500 hover:bg-gray-50")
                                        )}
                                    >
                                        <TrendingDown className="h-8 w-8" />
                                        <span className="font-semibold">Pengeluaran</span>
                                    </button>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Main Form */}
                        <Card className={cn(
                            "mb-6 border backdrop-blur-sm",
                            isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                        )}>
                            <CardContent className="p-6 space-y-5">
                                {/* Datetime */}
                                <div className="space-y-2">
                                    <Label className={isDark ? "text-gray-300" : ""}>
                                        Tanggal & Waktu <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        type="datetime-local"
                                        value={transactionDatetime}
                                        onChange={(e) => setTransactionDatetime(e.target.value)}
                                        className={cn(
                                            isDark ? "bg-gray-800 border-gray-700 text-white" : ""
                                        )}
                                        required
                                    />
                                </div>

                                {/* Category with Search */}
                                <div className="space-y-2">
                                    <Label className={isDark ? "text-gray-300" : ""}>
                                        Kategori <span className="text-red-500">*</span>
                                    </Label>
                                    <div className="relative">
                                        <div
                                            className={cn(
                                                "flex items-center gap-2 h-11 px-3 rounded-md border cursor-pointer",
                                                isDark ? "bg-gray-800 border-gray-700" : "bg-white border-gray-200"
                                            )}
                                            onClick={() => setShowCategoryDropdown(!showCategoryDropdown)}
                                        >
                                            {selectedCategory ? (
                                                <span className={isDark ? "text-white" : "text-gray-900"}>
                                                    {selectedCategory.name}
                                                </span>
                                            ) : (
                                                <span className={isDark ? "text-gray-500" : "text-gray-400"}>
                                                    Pilih kategori...
                                                </span>
                                            )}
                                        </div>

                                        {showCategoryDropdown && (
                                            <div className={cn(
                                                "absolute z-20 w-full mt-1 rounded-md border shadow-lg max-h-60 overflow-auto",
                                                isDark ? "bg-gray-800 border-gray-700" : "bg-white border-gray-200"
                                            )}>
                                                <div className="p-2 border-b border-gray-700">
                                                    <div className="relative">
                                                        <Search className={cn(
                                                            "absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4",
                                                            isDark ? "text-gray-500" : "text-gray-400"
                                                        )} />
                                                        <Input
                                                            placeholder="Cari kategori..."
                                                            value={categorySearch}
                                                            onChange={(e) => setCategorySearch(e.target.value)}
                                                            className={cn(
                                                                "pl-9 h-9",
                                                                isDark ? "bg-gray-900 border-gray-700 text-white" : ""
                                                            )}
                                                            autoFocus
                                                        />
                                                    </div>
                                                </div>

                                                {filteredCategories.length > 0 ? (
                                                    filteredCategories.map(cat => (
                                                        <div
                                                            key={cat.id}
                                                            onClick={() => handleSelectCategory(cat)}
                                                            className={cn(
                                                                "px-4 py-2.5 cursor-pointer transition-colors",
                                                                isDark ? "hover:bg-gray-700 text-white" : "hover:bg-gray-100 text-gray-900",
                                                                cat.id === categoryId && (isDark ? "bg-indigo-900/30" : "bg-indigo-50")
                                                            )}
                                                        >
                                                            {cat.name}
                                                        </div>
                                                    ))
                                                ) : (
                                                    <div className={cn("px-4 py-3 text-sm", isDark ? "text-gray-400" : "text-gray-500")}>
                                                        Kategori tidak ditemukan
                                                    </div>
                                                )}
                                            </div>
                                        )}
                                    </div>
                                </div>

                                {/* Amount */}
                                <div className="space-y-2">
                                    <Label className={isDark ? "text-gray-300" : ""}>
                                        Jumlah <span className="text-red-500">*</span>
                                    </Label>
                                    <div className="relative">
                                        <span className={cn(
                                            "absolute left-3 top-1/2 -translate-y-1/2 font-medium",
                                            isDark ? "text-gray-400" : "text-gray-500"
                                        )}>Rp</span>
                                        <Input
                                            type="text"
                                            inputMode="numeric"
                                            placeholder="0"
                                            value={formatDisplayAmount(amount)}
                                            onChange={handleAmountChange}
                                            className={cn(
                                                "pl-10 text-right font-mono text-lg h-12",
                                                isDark ? "bg-gray-800 border-gray-700 text-white" : ""
                                            )}
                                        />
                                    </div>
                                </div>

                                {/* Description */}
                                <div className="space-y-2">
                                    <Label className={isDark ? "text-gray-300" : ""}>
                                        Uraian / Keterangan <span className="text-red-500">*</span>
                                    </Label>
                                    <textarea
                                        value={description}
                                        onChange={(e) => setDescription(e.target.value)}
                                        rows={3}
                                        className={cn(
                                            "w-full px-3 py-2 rounded-md border text-sm resize-none",
                                            isDark
                                                ? "bg-gray-800 border-gray-700 text-white placeholder:text-gray-500"
                                                : "border-gray-200 placeholder:text-gray-400"
                                        )}
                                    />
                                </div>
                            </CardContent>
                        </Card>

                        {/* Submit Buttons */}
                        <div className="flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => router.visit(route('finance.transactions.show', transaction.id))}
                                className={cn(
                                    "sm:order-1",
                                    isDark ? "border-gray-700 text-gray-300" : ""
                                )}
                            >
                                Batal
                            </Button>
                            <Button
                                type="submit"
                                disabled={!isValid || isSubmitting}
                                className={cn(
                                    "sm:order-2 bg-indigo-600 hover:bg-indigo-700 text-white",
                                    !isValid && "opacity-50 cursor-not-allowed"
                                )}
                            >
                                {isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'}
                            </Button>
                        </div>
                    </form>
                </main>
            </div>
        </>
    );
}
