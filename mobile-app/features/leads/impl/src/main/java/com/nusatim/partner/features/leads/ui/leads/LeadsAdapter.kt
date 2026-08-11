package com.nusatim.partner.features.leads.ui.leads

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.LeadResponse
import com.nusatim.partner.core.ui.util.StatusUiMapper
import com.nusatim.partner.features.leads.databinding.ItemLeadBinding
import java.text.NumberFormat
import java.util.*

class LeadsAdapter(
    private val onItemClick: (LeadResponse) -> Unit
) : ListAdapter<LeadResponse, LeadsAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemLeadBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemLeadBinding) : RecyclerView.ViewHolder(binding.root) {
        init {
            binding.root.setOnClickListener {
                val position = adapterPosition
                if (position != RecyclerView.NO_POSITION) {
                    onItemClick(getItem(position))
                }
            }
        }

        fun bind(item: LeadResponse) {
            binding.tvName.text = item.name ?: "-"
            binding.tvPhone.text = item.phone ?: "-"
            binding.tvProduct.text = item.serviceName
            binding.tvEstimation.text = formatRupiah(item.estimatedValue ?: 0)

            // Centralized Status Mapping
            val context = binding.root.context
            val statusUi = StatusUiMapper.mapStatus(item.status)
            val color = androidx.core.content.ContextCompat.getColor(context, statusUi.colorRes)

            binding.tvStatusText.text = context.getString(statusUi.labelRes)
            binding.ivStatusDot.imageTintList = android.content.res.ColorStateList.valueOf(color)
            binding.tvStatusText.setTextColor(color)
            binding.cardStatus.backgroundTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(context, statusUi.backgroundRes)
            )

            // Icon Styling
            binding.cardIcon.backgroundTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(context, com.nusatim.partner.core.ui.R.color.project_orange_light)
            )
            binding.ivIcon.imageTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(context, com.nusatim.partner.core.ui.R.color.partner_primary)
            )

            // Product Styling
            binding.ivTag.imageTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(context, com.nusatim.partner.core.ui.R.color.partner_primary)
            )
        }
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }

    class DiffCallback : DiffUtil.ItemCallback<LeadResponse>() {
        override fun areItemsTheSame(oldItem: LeadResponse, newItem: LeadResponse): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: LeadResponse, newItem: LeadResponse): Boolean {
            return oldItem == newItem
        }
    }
}
