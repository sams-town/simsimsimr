import * as React from "react"
import { Slot } from "@radix-ui/react-slot"
import { cva, type VariantProps } from "class-variance-authority"

import { cn } from "@/lib/utils"

const buttonVariants = cva(
    "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg text-sm font-medium transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0",
    {
        variants: {
            variant: {
                default:
                    "bg-primary text-white shadow-md shadow-primary/25 hover:bg-primary-dark hover:shadow-lg hover:shadow-primary/30",
                destructive:
                    "bg-danger text-white shadow-md shadow-danger/25 hover:bg-danger/90 hover:shadow-lg",
                outline:
                    "border border-gray-200 bg-white shadow-sm hover:bg-gray-50 hover:border-gray-300 text-gray-700",
                secondary:
                    "bg-secondary text-white shadow-md shadow-secondary/25 hover:bg-secondary-dark",
                ghost:
                    "hover:bg-gray-100 hover:text-gray-900",
                link:
                    "text-primary underline-offset-4 hover:underline",
                success:
                    "bg-success text-white shadow-md shadow-success/25 hover:bg-success/90",
            },
            size: {
                default: "h-10 px-5 py-2",
                sm: "h-8 rounded-md px-3 text-xs",
                lg: "h-12 rounded-lg px-8 text-base",
                icon: "h-10 w-10",
            },
        },
        defaultVariants: {
            variant: "default",
            size: "default",
        },
    }
)

export interface ButtonProps
    extends React.ButtonHTMLAttributes<HTMLButtonElement>,
    VariantProps<typeof buttonVariants> {
    asChild?: boolean
    loading?: boolean
}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
    ({ className, variant, size, asChild = false, loading, children, disabled, ...props }, ref) => {
        const Comp = asChild ? Slot : "button"
        return (
            <Comp
                className={cn(buttonVariants({ variant, size, className }))}
                ref={ref}
                disabled={disabled || loading}
                {...props}
            >
                {loading ? (
                    <>
                        <svg className="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading...</span>
                    </>
                ) : children}
            </Comp>
        )
    }
)
Button.displayName = "Button"

export { Button, buttonVariants }
