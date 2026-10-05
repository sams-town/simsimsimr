import { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';

const AccountPayableList = () => {
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(setPageTitle('Account Payable List'));
        dispatch(setTitle('Account Payable List'));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Settings', 'Account Payable List']));
    }, [dispatch]);

    return (
        <div>
            <div className="panel mt-6">
                <h2 className="text-xl font-semibold mb-4">Account Payable List</h2>
                <p>Account Payable List page content goes here.</p>
            </div>
        </div>
    );
};

export default AccountPayableList;
