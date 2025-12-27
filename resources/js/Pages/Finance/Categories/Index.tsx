import { Head, router } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { ThemeToggle } from '@/Components/theme-toggle';
import { useIsDark } from '@/hooks/use-is-dark';
import { ArrowLeft, Plus, Edit, Trash2, TrendingUp, TrendingDown } from 'lucide-react';
import { cn } from '@/lib/utils';
import { useState } from 'react';

interface Category {
    id: number;
    name: string;
    type: string;
    type_label: string;
    description: string | null;
    is_active: boolean;
    sort_order: number;
}

interface Props {
    categories: Category[];
}

export default function CategoryIndex({ categories }: Props) {
    const isDark = useIsDark();
    const [showForm, setShowForm] = useState(false);
    const [editingId, setEditingId] = useState<number | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Form state
    const [formName, setFormName] = useState('');
    const [formType, setFormType] = useState<'income' | 'expense'>('income');
    const [formDescription, setFormDescription] = useState('');

    // Group categories
    const incomeCategories = categories.filter(c => c.type === 'income');
    const expenseCategories = categories.filter(c => c.type === 'expense');

    const resetForm = () => {
        setFormName('');
        setFormType('income');
        setFormDescription('');
        setEditingId(null);
        setShowForm(false);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!formName.trim()) return;

        setIsSubmitting(true);

        if (editingId) {
            router.put(route('finance.categories.update', editingId), {
                name: formName.trim(),
                description: formDescription.trim() || null,
            }, {
                onSuccess: () => resetForm(),
                onFinish: () => setIsSubmitting(false),
            });
        } else {
            router.post(route('finance.categories.store'), {
                name: formName.trim(),
                type: formType,
                description: formDescription.trim() || null,
            }, {
                onSuccess: () => resetForm(),
                onFinish: () => setIsSubmitting(false),
            });
        }
    };

    const handleEdit = (cat: Category) => {
        setEditingId(cat.id);
        setFormName(cat.name);
        setFormType(cat.type as 'income' | 'expense');
        setFormDescription(cat.description || '');
        setShowForm(true);
    };

    const handleDelete = (cat: Category) => {
        if (confirm(`Hapus kategori "${cat.name}"?`)) {
            router.delete(route('finance.categories.destroy', cat.id));
        }
    };

    const CategoryList = ({ items, type }: { items: Category[]; type: 'income' | 'expense' }) => (
        <Card className={cn(
            "border backdrop-blur-sm",
            isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
        )}>
            <CardHeader className="pb-3">
                <CardTitle className={cn(
                    "flex items-center gap-2 text-lg",
                    type === 'income'
                        ? (isDark ? "text-green-400" : "text-green-600")
                        : (isDark ? "text-red-400" : "text-red-600")
                )}>
                    {type === 'income' ? <TrendingUp className="h-5 w-5" /> : <TrendingDown className="h-5 w-5" />}
                    {type === 'income' ? 'Pemasukan' : 'Pengeluaran'}
                    <span className={cn("text-sm font-normal", isDark ? "text-gray-500" : "text-gray-400")}>
                        ({items.length})
                    </span>
                </CardTitle>
            </CardHeader>
            <CardContent className="pt-0">
                {items.length > 0 ? (
                    <div className="space-y-2">
                        {items.map(cat => (
                            <div
                                key={cat.id}
                                className={cn(
                                    "flex items-center justify-between p-3 rounded-lg",
                                    isDark ? "bg-gray-800/50" : "bg-gray-50"
                                )}
                            >
                                <div>
                                    <p className={cn("font-medium", isDark ? "text-white" : "text-gray-900")}>
                                        {cat.name}
                                    </p>
                                    {cat.description && (
                                        <p className={cn("text-sm", isDark ? "text-gray-400" : "text-gray-500")}>
                                            {cat.description}
                                        </p>
                                    )}
                                </div>
                                <div className="flex gap-1">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        className={cn("h-8 w-8", isDark ? "text-gray-400 hover:text-white" : "text-gray-500 hover:text-gray-900")}
                                        onClick={() => handleEdit(cat)}
                                    >
                                        <Edit className="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        className={cn("h-8 w-8", isDark ? "text-gray-400 hover:text-red-400" : "text-gray-500 hover:text-red-600")}
                                        onClick={() => handleDelete(cat)}
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className={cn("text-sm text-center py-4", isDark ? "text-gray-500" : "text-gray-400")}>
                        Belum ada kategori
                    </p>
                )}
            </CardContent>
        </Card>
    );

    return (
        <>
            <Head title="Kategori Transaksi" />

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
                    <div className="max-w-4xl mx-auto px-4 sm:px-6">
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
                                    <h1 className={cn("text-lg md:text-xl font-bold", isDark ? "text-white" : "text-gray-900")}>
                                        Kategori Transaksi
                                    </h1>
                                    <p className={cn("text-xs", isDark ? "text-gray-400" : "text-gray-500")}>
                                        Kelola kategori pemasukan & pengeluaran
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <ThemeToggle />
                                <Button
                                    onClick={() => { resetForm(); setShowForm(true); }}
                                    className="bg-indigo-600 hover:bg-indigo-700 text-white"
                                >
                                    <Plus className="h-4 w-4 mr-2" />
                                    Tambah
                                </Button>
                            </div>
                        </div>
                    </div>
                </header>

                <main className="max-w-4xl mx-auto py-6 px-4 sm:px-6">
                    {/* Add/Edit Form */}
                    {showForm && (
                        <Card className={cn(
                            "mb-6 border backdrop-blur-sm",
                            isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                        )}>
                            <CardHeader className="pb-3">
                                <CardTitle className={cn("text-lg", isDark ? "text-white" : "text-gray-900")}>
                                    {editingId ? 'Edit Kategori' : 'Tambah Kategori'}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <form onSubmit={handleSubmit} className="space-y-4">
                                    {!editingId && (
                                        <div className="space-y-2">
                                            <Label className={isDark ? "text-gray-300" : ""}>Tipe</Label>
                                            <div className="flex gap-4">
                                                <label className="flex items-center gap-2 cursor-pointer">
                                                    <input
                                                        type="radio"
                                                        name="type"
                                                        value="income"
                                                        checked={formType === 'income'}
                                                        onChange={() => setFormType('income')}
                                                        className="text-green-600"
                                                    />
                                                    <span className={cn(
                                                        "flex items-center gap-1",
                                                        isDark ? "text-green-400" : "text-green-600"
                                                    )}>
                                                        <TrendingUp className="h-4 w-4" /> Pemasukan
                                                    </span>
                                                </label>
                                                <label className="flex items-center gap-2 cursor-pointer">
                                                    <input
                                                        type="radio"
                                                        name="type"
                                                        value="expense"
                                                        checked={formType === 'expense'}
                                                        onChange={() => setFormType('expense')}
                                                        className="text-red-600"
                                                    />
                                                    <span className={cn(
                                                        "flex items-center gap-1",
                                                        isDark ? "text-red-400" : "text-red-600"
                                                    )}>
                                                        <TrendingDown className="h-4 w-4" /> Pengeluaran
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    )}
                                    <div className="space-y-2">
                                        <Label className={isDark ? "text-gray-300" : ""}>Nama Kategori *</Label>
                                        <Input
                                            value={formName}
                                            onChange={(e) => setFormName(e.target.value)}
                                            placeholder="Contoh: Bayar SPP"
                                            className={isDark ? "bg-gray-800 border-gray-700 text-white" : ""}
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <Label className={isDark ? "text-gray-300" : ""}>Deskripsi (opsional)</Label>
                                        <Input
                                            value={formDescription}
                                            onChange={(e) => setFormDescription(e.target.value)}
                                            placeholder="Keterangan singkat"
                                            className={isDark ? "bg-gray-800 border-gray-700 text-white" : ""}
                                        />
                                    </div>
                                    <div className="flex gap-3 justify-end">
                                        <Button type="button" variant="outline" onClick={resetForm} className={isDark ? "border-gray-700 text-gray-300" : ""}>
                                            Batal
                                        </Button>
                                        <Button type="submit" disabled={!formName.trim() || isSubmitting} className="bg-indigo-600 hover:bg-indigo-700 text-white">
                                            {isSubmitting ? 'Menyimpan...' : 'Simpan'}
                                        </Button>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    )}

                    {/* Category Lists */}
                    <div className="grid md:grid-cols-2 gap-6">
                        <CategoryList items={incomeCategories} type="income" />
                        <CategoryList items={expenseCategories} type="expense" />
                    </div>
                </main>
            </div>
        </>
    );
}
