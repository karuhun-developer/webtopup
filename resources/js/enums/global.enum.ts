interface Enum {
    [key: string]: {
        value: number | string;
        label: string;
    };
}

export const CommonStatusEnum: Enum = {
    ACTIVE: {
        value: 1,
        label: 'Active',
    },
    INACTIVE: {
        value: 0,
        label: 'Inactive',
    },
};

export const ValidationEnum: Enum = {
    PENDING: {
        value: 0,
        label: 'Pending',
    },
    APPROVED: {
        value: 1,
        label: 'Approved',
    },
    REJECTED: {
        value: 2,
        label: 'Rejected',
    },
};
