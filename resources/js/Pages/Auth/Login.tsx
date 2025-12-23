import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { ThemeToggle } from '@/Components/theme-toggle';
import { useIsDark } from '@/hooks/use-is-dark';
import { Phone, Lock, Loader2 } from 'lucide-react';
import { cn } from '@/lib/utils';

export default function Login() {
    const { data, setData, post, processing, errors, reset } = useForm({
        phone: '',
        password: '',
    });

    const isDark = useIsDark();

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <>
            <Head title="Masuk Aplikasi" />

            {/* Background - Changes based on theme */}
            <div className="fixed inset-0">
                {/* Light Mode Background - Colorful Gradient with Animated Blobs */}
                <div className={cn(
                    "absolute inset-0 transition-opacity duration-300",
                    isDark ? "opacity-0 pointer-events-none" : "opacity-100"
                )}>
                    <div className="absolute inset-0 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500"></div>
                    <div className="absolute top-0 -left-4 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                    <div className="absolute top-0 -right-4 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                    <div className="absolute -bottom-8 left-20 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
                </div>

                {/* Dark Mode Background - Deep Dark with Subtle Glows */}
                <div className={cn(
                    "absolute inset-0 transition-opacity duration-300",
                    isDark ? "opacity-100" : "opacity-0 pointer-events-none"
                )}>
                    <div className="absolute inset-0 bg-gradient-to-br from-gray-950 via-slate-900 to-gray-950"></div>
                    <div className="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-900/30 rounded-full blur-3xl"></div>
                    <div className="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-900/20 rounded-full blur-3xl"></div>
                    <div className="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:50px_50px]"></div>
                </div>
            </div>

            {/* Content Container */}
            <div className="relative min-h-screen flex items-center justify-center p-4">
                {/* Theme Toggle */}
                <div className="fixed top-4 right-4 z-50">
                    <ThemeToggle />
                </div>

                {/* Login Card */}
                <div className="w-full max-w-sm md:max-w-md relative z-10">
                    <Card className={cn(
                        "border-0 shadow-2xl backdrop-blur-xl",
                        isDark
                            ? "bg-gray-900/90 border border-gray-800"
                            : "bg-white/90"
                    )}>
                        <CardHeader className="space-y-2 text-center pb-2 px-6 pt-6 md:px-8 md:pt-8">
                            {/* Logo */}
                            <div className={cn(
                                "mx-auto w-16 h-16 md:w-20 md:h-20 rounded-2xl flex items-center justify-center mb-3",
                                isDark
                                    ? "bg-gradient-to-br from-indigo-600 to-purple-700 shadow-lg shadow-indigo-900/50"
                                    : "bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/30"
                            )}>
                                <span className="text-2xl md:text-3xl">🕌</span>
                            </div>
                            <CardTitle className={cn(
                                "text-2xl md:text-3xl font-bold tracking-tight",
                                isDark ? "text-white" : "text-gray-900"
                            )}>
                                Baitul Jannah
                            </CardTitle>
                            <CardDescription className={cn(
                                "text-sm md:text-base",
                                isDark ? "text-gray-400" : "text-gray-600"
                            )}>
                                Super App Management System
                            </CardDescription>
                        </CardHeader>

                        <form onSubmit={submit}>
                            <CardContent className="space-y-5 px-6 md:px-8 pt-4">
                                {/* Phone Input */}
                                <div className="space-y-2">
                                    <Label htmlFor="phone" className={cn(
                                        "font-medium",
                                        isDark ? "text-gray-200" : "text-gray-700"
                                    )}>
                                        Nomor HP
                                    </Label>
                                    <div className="relative">
                                        <Phone className={cn(
                                            "absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5",
                                            isDark ? "text-gray-500" : "text-gray-400"
                                        )} />
                                        <Input
                                            id="phone"
                                            type="tel"
                                            placeholder="08123456789"
                                            className={cn(
                                                "pl-12 h-12 text-base rounded-xl",
                                                isDark
                                                    ? "border-gray-700 bg-gray-800/50 text-white placeholder:text-gray-500"
                                                    : "border-gray-200 bg-gray-50 text-gray-900 placeholder:text-gray-400",
                                                "focus:ring-2 focus:ring-indigo-500 focus:border-transparent",
                                                errors.phone && "border-red-500 focus:ring-red-500"
                                            )}
                                            value={data.phone}
                                            onChange={(e) => setData('phone', e.target.value)}
                                            autoFocus
                                        />
                                    </div>
                                    {errors.phone && (
                                        <p className="text-sm text-red-500 font-medium">{errors.phone}</p>
                                    )}
                                </div>

                                {/* Password Input */}
                                <div className="space-y-2">
                                    <Label htmlFor="password" className={cn(
                                        "font-medium",
                                        isDark ? "text-gray-200" : "text-gray-700"
                                    )}>
                                        Password
                                    </Label>
                                    <div className="relative">
                                        <Lock className={cn(
                                            "absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5",
                                            isDark ? "text-gray-500" : "text-gray-400"
                                        )} />
                                        <Input
                                            id="password"
                                            type="password"
                                            placeholder="••••••••"
                                            className={cn(
                                                "pl-12 h-12 text-base rounded-xl",
                                                isDark
                                                    ? "border-gray-700 bg-gray-800/50 text-white placeholder:text-gray-500"
                                                    : "border-gray-200 bg-gray-50 text-gray-900 placeholder:text-gray-400",
                                                "focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            )}
                                            value={data.password}
                                            onChange={(e) => setData('password', e.target.value)}
                                        />
                                    </div>
                                    {errors.password && (
                                        <p className="text-sm text-red-500 font-medium">{errors.password}</p>
                                    )}
                                </div>
                            </CardContent>

                            <CardFooter className="flex-col gap-4 px-6 pb-6 pt-2 md:px-8 md:pb-8">
                                <Button
                                    type="submit"
                                    className={cn(
                                        "w-full h-12 text-base font-semibold rounded-xl text-white",
                                        isDark
                                            ? "bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 shadow-lg shadow-indigo-500/20"
                                            : "bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-lg shadow-indigo-500/30"
                                    )}
                                    disabled={processing}
                                >
                                    {processing ? (
                                        <>
                                            <Loader2 className="mr-2 h-5 w-5 animate-spin" />
                                            Memproses...
                                        </>
                                    ) : (
                                        'Masuk Aplikasi'
                                    )}
                                </Button>
                            </CardFooter>
                        </form>
                    </Card>

                    {/* Footer */}
                    <p className={cn(
                        "mt-6 text-center text-sm",
                        isDark ? "text-gray-500" : "text-white/80"
                    )}>
                        © {new Date().getFullYear()} Yayasan Baitul Jannah Berilmu
                    </p>
                </div>
            </div>

            {/* Animations */}
            <style>{`
                @keyframes blob {
                    0% { transform: translate(0px, 0px) scale(1); }
                    33% { transform: translate(30px, -50px) scale(1.1); }
                    66% { transform: translate(-20px, 20px) scale(0.9); }
                    100% { transform: translate(0px, 0px) scale(1); }
                }
                .animate-blob { animation: blob 7s infinite; }
                .animation-delay-2000 { animation-delay: 2s; }
                .animation-delay-4000 { animation-delay: 4s; }
            `}</style>
        </>
    );
}
