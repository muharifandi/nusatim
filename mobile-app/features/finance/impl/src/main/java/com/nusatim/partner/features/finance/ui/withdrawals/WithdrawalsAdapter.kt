package com.nusatim.partner.features.finance.ui.withdrawals

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.WithdrawalResponse
import com.nusatim.partner.features.finance.databinding.ItemWithdrawalBinding
import java.text.NumberFormat
import java.util.*

class WithdrawalsAdapter(
    private val onItemClick: (WithdrawalResponse) -> Unit
) : ListAdapter<WithdrawalResponse, WithdrawalsAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemWithdrawalBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemWithdrawalBinding) : RecyclerView.ViewHolder(binding.root) {
        init {
            binding.root.setOnClickListener {
                val pos = adapterPosition
                if (pos != RecyclerView.NO_POSITION) onItemClick(getItem(pos))
            }
        }

        fun bind(item: WithdrawalResponse) {
            binding.tvAmount.text = formatRupiah(item.amount)
            binding.tvDate.text = item.createdAt
            
            // Status Badge
            binding.tvStatusText.text = item.status?.replaceFirstChar { it.uppercase() } ?: "-"
            
            val (statusColor, statusBg, statusIcon) = when (item.status?.lowercase()) {
                "pending" -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_warning,
                    com.nusatim.partner.core.ui.R.color.project_orange_light,
                    com.nusatim.partner.core.ui.R.drawable.ic_status_pending
                )
                "approved" -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_info,
                    com.nusatim.partner.core.ui.R.color.project_blue_light,
                    com.nusatim.partner.core.ui.R.drawable.ic_check_circle
                )
                "paid" -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_success,
                    com.nusatim.partner.core.ui.R.color.project_green_light,
                    com.nusatim.partner.core.ui.R.drawable.ic_check_circle
                )
                "rejected" -> Triple(
                    com.nusatim.partner.core.ui.R.color.partner_error,
                    com.nusatim.partner.core.ui.R.color.partner_surface_variant,
                    com.nusatim.partner.core.ui.R.drawable.ic_status_rejected
                )
                else -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_neutral,
                    com.nusatim.partner.core.ui.R.color.partner_surface_variant,
                    com.nusatim.partner.core.ui.R.drawable.ic_status_pending
                )
            }
            
            binding.cardStatus.backgroundTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(binding.root.context, statusBg)
            )
            binding.tvStatusText.setTextColor(
                androidx.core.content.ContextCompat.getColor(binding.root.context, statusColor)
            )
            binding.ivStatusIcon.setImageResource(statusIcon)
            binding.ivStatusIcon.imageTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(binding.root.context, statusColor)
            )

            // Icon Background
            binding.cardIcon.backgroundTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(
                    binding.root.context,
                    com.nusatim.partner.core.ui.R.color.project_orange_light
                )
            )
            binding.ivIcon.imageTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(
                    binding.root.context,
                    com.nusatim.partner.core.ui.R.color.partner_primary
                )
            )
        }
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }

    class DiffCallback : DiffUtil.ItemCallback<WithdrawalResponse>() {
        override fun areItemsTheSame(oldItem: WithdrawalResponse, newItem: WithdrawalResponse): Boolean = oldItem.id == newItem.id
        override fun areContentsTheSame(oldItem: WithdrawalResponse, newItem: WithdrawalResponse): Boolean = oldItem == newItem
    }
}
