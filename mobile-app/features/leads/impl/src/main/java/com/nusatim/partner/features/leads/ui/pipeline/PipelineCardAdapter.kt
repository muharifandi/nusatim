package com.nusatim.partner.features.leads.ui.pipeline

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.LeadResponse
import com.nusatim.partner.features.leads.databinding.ItemPipelineCardBinding
import java.text.NumberFormat
import java.util.*

class PipelineCardAdapter(
    private val status: String,
    private val onItemClick: (LeadResponse) -> Unit,
    private val onItemLongClick: (LeadResponse) -> Unit
) : ListAdapter<LeadResponse, PipelineCardAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemPipelineCardBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemPipelineCardBinding) : RecyclerView.ViewHolder(binding.root) {
        init {
            binding.root.setOnClickListener {
                val pos = adapterPosition
                if (pos != RecyclerView.NO_POSITION) onItemClick(getItem(pos))
            }
            binding.root.setOnLongClickListener {
                val pos = adapterPosition
                if (pos != RecyclerView.NO_POSITION) onItemLongClick(getItem(pos))
                true
            }
        }

        fun bind(item: LeadResponse) {
            binding.tvName.text = item.name ?: "-"
            binding.tvPhone.text = item.phone ?: "-"
            binding.tvEstimation.text = formatRupiah(item.estimatedValue ?: 0)
            
            // Dynamic Card Styling based on Column Status
            val colorTint = when (status.lowercase()) {
                "new" -> com.nusatim.partner.core.ui.R.color.partner_primary
                "contacted" -> com.nusatim.partner.core.ui.R.color.status_info
                "opportunity" -> com.nusatim.partner.core.ui.R.color.status_success
                "lost", "rejected" -> com.nusatim.partner.core.ui.R.color.partner_error
                else -> com.nusatim.partner.core.ui.R.color.status_info
            }
            
            val context = binding.root.context
            val tint = androidx.core.content.ContextCompat.getColor(context, colorTint)
            val bgTint = android.content.res.ColorStateList.valueOf(tint).withAlpha(26) // ~10% alpha

            binding.cardProfile.backgroundTintList = bgTint
            binding.ivProfile.imageTintList = android.content.res.ColorStateList.valueOf(tint)
            
            binding.cardWallet.backgroundTintList = bgTint
            binding.ivWallet.imageTintList = android.content.res.ColorStateList.valueOf(tint)
            
            binding.tvEstimation.setTextColor(tint)
        }
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }

    class DiffCallback : DiffUtil.ItemCallback<LeadResponse>() {
        override fun areItemsTheSame(oldItem: LeadResponse, newItem: LeadResponse): Boolean = oldItem.id == newItem.id
        override fun areContentsTheSame(oldItem: LeadResponse, newItem: LeadResponse): Boolean = oldItem == newItem
    }
}
