import * as React from "react"
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "./dialog"
import { Button } from "./button"
import { AlertCircle, CheckCircle, AlertTriangle } from "lucide-react"

interface AlertDialogProps {
    isOpen: boolean
    onClose: () => void
    onConfirm: () => void
    title?: string
    message?: string
    confirmText?: string
    cancelText?: string
    loading?: boolean
    variant?: "default" | "danger" | "success" | "warning"
}

const variantConfig = {
    default: {
        icon: AlertCircle,
        iconColor: "text-primary",
        iconBg: "bg-primary/10",
        buttonVariant: "default" as const,
    },
    danger: {
        icon: AlertTriangle,
        iconColor: "text-danger",
        iconBg: "bg-danger/10",
        buttonVariant: "destructive" as const,
    },
    success: {
        icon: CheckCircle,
        iconColor: "text-success",
        iconBg: "bg-success/10",
        buttonVariant: "success" as const,
    },
    warning: {
        icon: AlertTriangle,
        iconColor: "text-warning",
        iconBg: "bg-warning/10",
        buttonVariant: "default" as const,
    },
}

export function AlertDialog({
    isOpen,
    onClose,
    onConfirm,
    title = "Confirmation",
    message = "Are you sure?",
    confirmText = "Confirm",
    cancelText = "Cancel",
    loading = false,
    variant = "default",
}: AlertDialogProps) {
    const config = variantConfig[variant]
    const Icon = config.icon

    return (
        <Dialog open={isOpen} onOpenChange={(open) => !open && !loading && onClose()}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader className="flex flex-col items-center text-center space-y-4">
                    <div className={`p-3 rounded-full ${config.iconBg}`}>
                        <Icon className={`h-6 w-6 ${config.iconColor}`} />
                    </div>
                    <div className="space-y-2">
                        <DialogTitle className="text-center">{title}</DialogTitle>
                        <DialogDescription className="text-center">
                            {message}
                        </DialogDescription>
                    </div>
                </DialogHeader>
                <DialogFooter className="mt-4 sm:justify-center gap-3">
                    <Button
                        variant="outline"
                        onClick={onClose}
                        disabled={loading}
                        className="min-w-[100px]"
                    >
                        {cancelText}
                    </Button>
                    <Button
                        variant={config.buttonVariant}
                        onClick={() => {
                            onConfirm()
                            onClose()
                        }}
                        loading={loading}
                        className="min-w-[100px]"
                    >
                        {confirmText}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    )
}
