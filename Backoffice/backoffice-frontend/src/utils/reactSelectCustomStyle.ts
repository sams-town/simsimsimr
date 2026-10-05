export const reactSelectCustomStyles = {
    control: (base: any, state: any) => ({
        ...base,
        borderColor: state.isFocused ? '#648de1' : '#e5e7eb',
        borderRadius: '0.5rem',
        minHeight: '2.5rem',
        height: '2.5rem',
        boxShadow: state.isFocused ? '0 0 0 2px rgba(100, 141, 225, 0.2)' : 'none',
        '&:hover': {
            borderColor: state.isFocused ? '#648de1' : '#d1d5db',
        },
        backgroundColor: 'white',
    }),
    valueContainer: (base: any) => ({
        ...base,
        paddingLeft: '1rem',
        paddingRight: '1rem',
    }),
    singleValue: (base: any) => ({
        ...base,
        fontSize: '0.875rem',
        color: '#1f2937',
    }),
    input: (base: any) => ({
        ...base,
        color: '#1f2937',
        fontSize: '0.875rem',
    }),
    placeholder: (base: any) => ({
        ...base,
        fontSize: '0.875rem',
        color: '#9ca3af',
    }),
    menu: (base: any) => ({
        ...base,
        zIndex: 50,
        fontSize: '0.875rem',
    }),
    menuPortal: (base: any) => ({ ...base, zIndex: 9999 })
};
