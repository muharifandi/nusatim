package com.nusatim.partner.core.common.util

object Constants {
    object Status {
        const val NEW = "new"
        const val OPEN = "open"
        const val DRAFT = "draft"
        const val PENDING = "pending"
        const val PENDING_REVIEW = "pending_review"
        const val PENDING_APPROVAL = "pending_approval"
        const val CONTACTED = "contacted"
        const val QUALIFIED = "qualified"
        const val OPPORTUNITY = "opportunity"
        const val PROPOSAL = "proposal"
        const val NEGOTIATION = "negotiation"
        const val ASSIGNED = "assigned"
        const val IN_PROGRESS = "in_progress"
        const val WAITING_CLIENT_PAYMENT = "waiting_client_payment"
        const val WAITING_PAYMENT = "waiting_payment"
        const val WON = "won"
        const val PAID = "paid"
        const val CLOSED = "closed"
        const val RESOLVED = "resolved"
        const val APPROVED = "approved"
        const val LOST = "lost"
        const val REJECTED = "rejected"
        const val CANCELLED = "cancelled"
        const val SUSPENDED = "suspended"
    }

    object PaymentStatus {
        const val UNPAID = "unpaid"
        const val PARTIAL = "partial"
        const val PAID = "paid"
    }

    object DocumentType {
        const val PHOTO = "photo"
        const val KTP = "ktp"
        const val NPWP = "npwp"
    }
}
