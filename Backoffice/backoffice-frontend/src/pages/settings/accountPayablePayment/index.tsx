import { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';

const AccountPayablePayment = () => {
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(setPageTitle('Account Payable Payment'));
        dispatch(setTitle('Account Payable Payment'));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Settings', 'Account Payable Payment']));
    }, [dispatch]);

    return (
        <div>
            <div className="panel mt-6">
                <h2 className="text-xl font-semibold mb-4">Account Payable Payment</h2>
                <p>Account Payable Payment page content goes here.</p>
            </div>
        </div>
    );
};

export default AccountPayablePayment;
