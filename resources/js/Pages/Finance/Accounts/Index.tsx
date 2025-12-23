import { Head, router } from '@inertiajs/react';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/Components/ui/table';
import { ThemeToggle } from '@/Components/theme-toggle';
import { useIsDark } from '@/hooks/use-is-dark';
import { ArrowLeft, Wallet, Plus } from 'lucide-react';
import { cn } from '@/lib/utils';

interface Account {
    id: number;
    code: string;
    name: string;
    type: string;
    type_label: string;
    formatted_balance: string;
    current_balance: number;
}

interface Props {
    accounts: Account[];
}

export default function AccountIndex({ accounts }: Props) {
    const isDark = useIsDark();

    return (
        <>
            <Head title="Chart of Accounts" />

            {/* Background Layers */}
            <div className="fixed inset-0">
                {/* Light Mode Background */}
                <div className={cn(
                    "absolute inset-0 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100",
                    isDark ? "opacity-0 pointer-events-none" : "opacity-100"
                )}></div>

                {/* Dark Mode Background */}
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
                {/* Top Bar */}
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
                                        Chart of Accounts
                                    </h1>
                                    <p className={cn("text-xs hidden sm:block", isDark ? "text-gray-400" : "text-gray-500")}>
                                        Kelola akun dan saldo keuangan
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <ThemeToggle />
                                <Button className="hidden sm:flex bg-indigo-600 hover:bg-indigo-700 text-white">
                                    <Plus className="h-4 w-4 mr-2" />
                                    Tambah Akun
                                </Button>
                            </div>
                        </div>
                    </div>
                </header>

                <main className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {/* Desktop View: Table */}
                    <div className="hidden md:block">
                        <Card className={cn("border backdrop-blur-sm overflow-hidden", isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200")}>
                            <Table>
                                <TableHeader>
                                    <TableRow className={cn("border-b", isDark ? "border-gray-800 bg-gray-800/50" : "border-gray-200 bg-gray-50")}>
                                        <TableHead className={cn("w-28 font-semibold", isDark ? "text-gray-300" : "text-gray-600")}>Kode</TableHead>
                                        <TableHead className={cn("font-semibold", isDark ? "text-gray-300" : "text-gray-600")}>Nama Akun</TableHead>
                                        <TableHead className={cn("w-36 font-semibold", isDark ? "text-gray-300" : "text-gray-600")}>Tipe</TableHead>
                                        <TableHead className={cn("text-right w-44 font-semibold", isDark ? "text-gray-300" : "text-gray-600")}>Saldo</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {accounts.map((account) => (
                                        <TableRow
                                            key={account.id}
                                            className={cn("border-b transition-colors cursor-pointer", isDark ? "border-gray-800 hover:bg-indigo-900/20" : "border-gray-100 hover:bg-indigo-50/50")}
                                        >
                                            <TableCell className={cn("font-mono font-bold", isDark ? "text-indigo-400" : "text-indigo-600")}>
                                                {account.code}
                                            </TableCell>
                                            <TableCell className={cn("font-medium", isDark ? "text-white" : "text-gray-900")}>
                                                {account.name}
                                            </TableCell>
                                            <TableCell>
                                                <span className={cn("inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold", isDark ? "bg-gray-800 text-gray-300" : "bg-gray-100 text-gray-700")}>
                                                    {account.type_label}
                                                </span>
                                            </TableCell>
                                            <TableCell className={cn("text-right font-mono font-semibold", isDark ? "text-white" : "text-gray-900")}>
                                                {account.formatted_balance}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </Card>
                    </div>

                    {/* Mobile View: Cards */}
                    <div className="space-y-3 md:hidden">
                        <Button className="w-full bg-indigo-600 hover:bg-indigo-700 text-white mb-4">
                            <Plus className="h-4 w-4 mr-2" />
                            Tambah Akun
                        </Button>

                        {accounts.map((account) => (
                            <Card
                                key={account.id}
                                className={cn("border backdrop-blur-sm overflow-hidden", isDark ? "bg-gray-900/50 border-gray-800 hover:border-indigo-700" : "bg-white border-gray-200 hover:border-indigo-300")}
                            >
                                <CardContent className="p-4">
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-center gap-2 mb-2">
                                                <div className={cn("w-9 h-9 rounded-xl flex items-center justify-center", isDark ? "bg-indigo-900/30" : "bg-indigo-100")}>
                                                    <Wallet className={cn("h-4 w-4", isDark ? "text-indigo-400" : "text-indigo-600")} />
                                                </div>
                                                <span className={cn("text-xs font-mono font-bold px-2 py-1 rounded-md", isDark ? "text-indigo-400 bg-indigo-900/20" : "text-indigo-600 bg-indigo-50")}>
                                                    {account.code}
                                                </span>
                                            </div>
                                            <h3 className={cn("font-semibold", isDark ? "text-white" : "text-gray-900")}>
                                                {account.name}
                                            </h3>
                                            <p className={cn("text-xs mt-0.5", isDark ? "text-gray-400" : "text-gray-500")}>
                                                {account.type_label}
                                            </p>
                                        </div>
                                        <div className="text-right">
                                            <p className={cn("text-lg font-bold", isDark ? "text-white" : "text-gray-900")}>
                                                {account.formatted_balance}
                                            </p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>

                    {/* Empty State */}
                    {accounts.length === 0 && (
                        <Card className={cn("p-12 text-center border backdrop-blur-sm", isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200")}>
                            <div className={cn("w-16 h-16 mx-auto rounded-2xl flex items-center justify-center mb-4", isDark ? "bg-gray-800" : "bg-gray-100")}>
                                <Wallet className="h-8 w-8 text-gray-400" />
                            </div>
                            <h3 className={cn("text-lg font-semibold mb-2", isDark ? "text-white" : "text-gray-900")}>
                                Belum ada akun
                            </h3>
                            <p className={cn("mb-6 max-w-sm mx-auto", isDark ? "text-gray-400" : "text-gray-500")}>
                                Mulai dengan menambahkan Chart of Accounts
                            </p>
                            <Button className="bg-indigo-600 hover:bg-indigo-700 text-white">
                                <Plus className="h-4 w-4 mr-2" />
                                Tambah Akun Pertama
                            </Button>
                        </Card>
                    )}
                </main>
            </div>
        </>
    );
}
