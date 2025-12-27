import { Head, Link, usePage, router } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { ThemeToggle } from '@/Components/theme-toggle';
import { useIsDark } from '@/hooks/use-is-dark';
import { Menu, X, LogOut, LayoutDashboard, Wallet, Users, TrendingUp, AlertCircle, ChevronRight, FileText } from 'lucide-react';
import { useState } from 'react';
import { cn } from '@/lib/utils';

export default function Dashboard() {
    const { auth } = usePage().props as any;
    const user = auth?.user;
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const isDark = useIsDark();

    const handleLogout = () => {
        router.post(route('logout'));
    };

    const navigation = [
        { name: 'Dashboard', href: route('dashboard'), icon: LayoutDashboard, current: true },
        { name: 'Buku Kas', href: route('finance.transactions.index'), icon: FileText, current: false },
    ];

    return (
        <>
            <Head title="Dashboard" />

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
                {/* Mobile Navigation Overlay */}
                {mobileMenuOpen && (
                    <div
                        className="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 md:hidden"
                        onClick={() => setMobileMenuOpen(false)}
                    />
                )}

                {/* Mobile Sidebar */}
                <div className={cn(
                    "fixed inset-y-0 left-0 w-72 shadow-2xl z-50 transform transition-transform duration-300 ease-out md:hidden",
                    isDark ? "bg-gray-900 border-r border-gray-800" : "bg-white border-r border-gray-200",
                    mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
                )}>
                    <div className={cn(
                        "flex items-center justify-between p-5 border-b",
                        isDark ? "border-gray-800" : "border-gray-200"
                    )}>
                        <span className={cn("text-xl font-bold", isDark ? "text-white" : "text-gray-900")}>
                            Menu
                        </span>
                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => setMobileMenuOpen(false)}
                            className={isDark ? "text-gray-400" : "text-gray-500"}
                        >
                            <X className="h-5 w-5" />
                        </Button>
                    </div>
                    <nav className="p-4 space-y-1">
                        {navigation.map((item) => (
                            <Link
                                key={item.name}
                                href={item.href}
                                className={cn(
                                    "flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-medium transition-all",
                                    item.current
                                        ? isDark ? "bg-indigo-900/30 text-indigo-400" : "bg-indigo-50 text-indigo-600"
                                        : isDark ? "text-gray-400 hover:bg-gray-800" : "text-gray-600 hover:bg-gray-100"
                                )}
                            >
                                <item.icon className="h-5 w-5" />
                                {item.name}
                            </Link>
                        ))}
                        <hr className={cn("my-4", isDark ? "border-gray-800" : "border-gray-200")} />
                        <button
                            onClick={handleLogout}
                            className={cn(
                                "flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-medium w-full",
                                isDark ? "text-red-400 hover:bg-red-900/20" : "text-red-600 hover:bg-red-50"
                            )}
                        >
                            <LogOut className="h-5 w-5" />
                            Keluar
                        </button>
                    </nav>
                </div>

                {/* Top Navbar */}
                <nav className={cn(
                    "sticky top-0 z-30 backdrop-blur-xl border-b",
                    isDark ? "bg-gray-900/80 border-gray-800/50" : "bg-white/80 border-gray-200/50"
                )}>
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex h-16 md:h-18 items-center justify-between">
                            {/* Left */}
                            <div className="flex items-center gap-4">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    className={cn("md:hidden h-10 w-10", isDark ? "text-gray-300" : "text-gray-600")}
                                    onClick={() => setMobileMenuOpen(true)}
                                >
                                    <Menu className="h-6 w-6" />
                                </Button>
                                <div className="flex items-center gap-3">
                                    <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                        <span className="text-lg">🕌</span>
                                    </div>
                                    <span className={cn("text-lg md:text-xl font-bold tracking-tight", isDark ? "text-white" : "text-gray-900")}>
                                        Baitul Jannah
                                    </span>
                                </div>
                            </div>

                            {/* Center - Desktop Nav */}
                            <div className={cn("hidden md:flex items-center gap-1 rounded-xl p-1", isDark ? "bg-gray-800" : "bg-gray-100")}>
                                {navigation.map((item) => (
                                    <Link
                                        key={item.name}
                                        href={item.href}
                                        className={cn(
                                            "px-4 py-2 rounded-lg text-sm font-medium transition-all",
                                            item.current
                                                ? isDark ? "bg-gray-700 text-white shadow-sm" : "bg-white text-gray-900 shadow-sm"
                                                : isDark ? "text-gray-400 hover:text-white" : "text-gray-600 hover:text-gray-900"
                                        )}
                                    >
                                        {item.name}
                                    </Link>
                                ))}
                            </div>

                            {/* Right */}
                            <div className="flex items-center gap-3">
                                <ThemeToggle />
                                <div className={cn("hidden sm:flex items-center gap-3 pl-3 border-l", isDark ? "border-gray-700" : "border-gray-200")}>
                                    <div className="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold">
                                        {user?.name?.charAt(0) || 'U'}
                                    </div>
                                    <span className={cn("text-sm font-medium", isDark ? "text-gray-300" : "text-gray-700")}>
                                        {user?.name?.split(' ')[0] || 'User'}
                                    </span>
                                </div>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    onClick={handleLogout}
                                    className={cn("hidden md:flex h-9 w-9", isDark ? "text-gray-400 hover:text-red-400" : "text-gray-500 hover:text-red-500")}
                                    title="Keluar"
                                >
                                    <LogOut className="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </nav>

                {/* Main Content */}
                <main className="max-w-7xl mx-auto py-6 md:py-8 px-4 sm:px-6 lg:px-8">
                    <div className="space-y-6">
                        {/* Welcome Hero */}
                        <div className={cn(
                            "relative overflow-hidden rounded-2xl p-6 md:p-8 shadow-xl",
                            isDark ? "bg-gradient-to-r from-indigo-900 via-purple-900 to-indigo-900" : "bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500"
                        )}>
                            <div className="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                            <div className="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

                            <div className="relative">
                                <p className="text-indigo-100 text-sm font-medium mb-1">Selamat Datang</p>
                                <h1 className="text-2xl md:text-4xl font-bold text-white mb-2">
                                    Ahlan wa Sahlan, {user?.name?.split(' ')[0] || 'User'}!
                                </h1>
                                <p className="text-indigo-100 text-sm md:text-base max-w-xl mb-4">
                                    Sistem Manajemen Terpadu Yayasan Baitul Jannah Berilmu
                                </p>
                                <Button
                                    className="bg-white/20 hover:bg-white/30 text-white border-0 backdrop-blur-sm"
                                    onClick={() => router.visit(route('finance.transactions.index'))}
                                >
                                    Lihat Keuangan
                                    <ChevronRight className="h-4 w-4 ml-1" />
                                </Button>
                            </div>
                        </div>

                        {/* Stats Grid */}
                        <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <Card className={cn("border backdrop-blur-sm", isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200")}>
                                <CardContent className="p-5">
                                    <div className="flex items-center justify-between mb-3">
                                        <div className={cn("w-10 h-10 rounded-xl flex items-center justify-center", isDark ? "bg-emerald-900/30" : "bg-emerald-100")}>
                                            <Wallet className={cn("h-5 w-5", isDark ? "text-emerald-400" : "text-emerald-600")} />
                                        </div>
                                        <span className={cn("flex items-center text-xs font-medium", isDark ? "text-emerald-400" : "text-emerald-600")}>
                                            <TrendingUp className="h-3 w-3 mr-1" />+15%
                                        </span>
                                    </div>
                                    <p className={cn("text-xs font-medium mb-1", isDark ? "text-gray-400" : "text-gray-500")}>Saldo Kas</p>
                                    <p className={cn("text-xl md:text-2xl font-bold", isDark ? "text-white" : "text-gray-900")}>Rp 12.5 Jt</p>
                                </CardContent>
                            </Card>

                            <Card className={cn("border backdrop-blur-sm", isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200")}>
                                <CardContent className="p-5">
                                    <div className="flex items-center justify-between mb-3">
                                        <div className={cn("w-10 h-10 rounded-xl flex items-center justify-center", isDark ? "bg-blue-900/30" : "bg-blue-100")}>
                                            <Users className={cn("h-5 w-5", isDark ? "text-blue-400" : "text-blue-600")} />
                                        </div>
                                    </div>
                                    <p className={cn("text-xs font-medium mb-1", isDark ? "text-gray-400" : "text-gray-500")}>Santri TPQ</p>
                                    <p className={cn("text-xl md:text-2xl font-bold", isDark ? "text-white" : "text-gray-900")}>45</p>
                                </CardContent>
                            </Card>

                            <Card className={cn("border backdrop-blur-sm", isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200")}>
                                <CardContent className="p-5">
                                    <div className="flex items-center justify-between mb-3">
                                        <div className={cn("w-10 h-10 rounded-xl flex items-center justify-center", isDark ? "bg-purple-900/30" : "bg-purple-100")}>
                                            <Users className={cn("h-5 w-5", isDark ? "text-purple-400" : "text-purple-600")} />
                                        </div>
                                    </div>
                                    <p className={cn("text-xs font-medium mb-1", isDark ? "text-gray-400" : "text-gray-500")}>Santri TAUD</p>
                                    <p className={cn("text-xl md:text-2xl font-bold", isDark ? "text-white" : "text-gray-900")}>32</p>
                                </CardContent>
                            </Card>

                            <Card className={cn("border backdrop-blur-sm", isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200")}>
                                <CardContent className="p-5">
                                    <div className="flex items-center justify-between mb-3">
                                        <div className={cn("w-10 h-10 rounded-xl flex items-center justify-center", isDark ? "bg-red-900/30" : "bg-red-100")}>
                                            <AlertCircle className={cn("h-5 w-5", isDark ? "text-red-400" : "text-red-600")} />
                                        </div>
                                    </div>
                                    <p className={cn("text-xs font-medium mb-1", isDark ? "text-gray-400" : "text-gray-500")}>Tunggakan</p>
                                    <p className={cn("text-xl md:text-2xl font-bold", isDark ? "text-red-400" : "text-red-600")}>3</p>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Quick Actions Mobile */}
                        <div className="grid grid-cols-2 gap-4 md:hidden">
                            <button
                                onClick={() => router.visit(route('finance.transactions.index'))}
                                className={cn(
                                    "flex flex-col items-center justify-center gap-3 p-6 rounded-2xl border transition-all",
                                    isDark ? "bg-gray-900/50 border-gray-800 hover:border-indigo-700" : "bg-white border-gray-200 hover:border-indigo-300"
                                )}
                            >
                                <div className={cn("w-12 h-12 rounded-xl flex items-center justify-center", isDark ? "bg-indigo-900/30" : "bg-indigo-100")}>
                                    <Wallet className={cn("h-6 w-6", isDark ? "text-indigo-400" : "text-indigo-600")} />
                                </div>
                                <span className={cn("text-sm font-medium", isDark ? "text-gray-300" : "text-gray-700")}>Keuangan</span>
                            </button>
                            <button className={cn(
                                "flex flex-col items-center justify-center gap-3 p-6 rounded-2xl border transition-all",
                                isDark ? "bg-gray-900/50 border-gray-800 hover:border-purple-700" : "bg-white border-gray-200 hover:border-purple-300"
                            )}>
                                <div className={cn("w-12 h-12 rounded-xl flex items-center justify-center", isDark ? "bg-purple-900/30" : "bg-purple-100")}>
                                    <Users className={cn("h-6 w-6", isDark ? "text-purple-400" : "text-purple-600")} />
                                </div>
                                <span className={cn("text-sm font-medium", isDark ? "text-gray-300" : "text-gray-700")}>Santri</span>
                            </button>
                        </div>
                    </div>
                </main>
            </div>
        </>
    );
}
