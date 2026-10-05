import * as React from "react"

import { cn } from "@/lib/utils"

const Input = React.forwardRef<HTMLInputElement, React.ComponentProps<"input">>(
    ({ className, type, ...props }, ref) => {
        return (
            <input
                type={type}
                className={cn(
                    "flex h-10 w-full rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-900 transition-all duration-200",
                    "placeholder:text-gray-400",
                    "hover:border-gray-300",
                    "focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none",
                    "disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500",
                    "read-only:bg-gray-50 read-only:text-gray-600",
                    "file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-gray-900",
                    className
                )}
                ref={ref}
                {...props}
            />
        )
    }
)
Input.displayName = "Input"

export { Input }
