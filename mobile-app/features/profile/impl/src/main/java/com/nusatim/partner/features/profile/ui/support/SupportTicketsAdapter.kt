package com.nusatim.partner.features.profile.ui.support

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.SupportTicketResponse
import com.nusatim.partner.features.profile.databinding.ItemSupportTicketBinding

class SupportTicketsAdapter(
    private val onItemClick: (SupportTicketResponse) -> Unit
) : ListAdapter<SupportTicketResponse, SupportTicketsAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemSupportTicketBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemSupportTicketBinding) : RecyclerView.ViewHolder(binding.root) {
        init {
            binding.root.setOnClickListener {
                val pos = adapterPosition
                if (pos != RecyclerView.NO_POSITION) onItemClick(getItem(pos))
            }
        }

        fun bind(item: SupportTicketResponse) {
            binding.tvSubject.text = item.subject
            binding.tvDescriptionSnippet.text = item.description
            binding.tvDate.text = item.createdAt
            
            binding.btnStatus.text = item.status.replaceFirstChar { it.uppercase() }
            
            val statusColor = when (item.status.lowercase()) {
                "open" -> com.nusatim.partner.core.ui.R.color.status_info
                "in_progress" -> com.nusatim.partner.core.ui.R.color.status_warning
                "resolved" -> com.nusatim.partner.core.ui.R.color.status_success
                "closed" -> com.nusatim.partner.core.ui.R.color.status_neutral
                else -> com.nusatim.partner.core.ui.R.color.status_neutral
            }
            binding.btnStatus.setBackgroundTintList(android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(binding.root.context, statusColor)
            ))
        }
    }

    class DiffCallback : DiffUtil.ItemCallback<SupportTicketResponse>() {
        override fun areItemsTheSame(oldItem: SupportTicketResponse, newItem: SupportTicketResponse): Boolean = oldItem.id == newItem.id
        override fun areContentsTheSame(oldItem: SupportTicketResponse, newItem: SupportTicketResponse): Boolean = oldItem == newItem
    }
}
